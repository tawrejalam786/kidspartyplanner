<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingConfirmationMailer;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    public function __construct(private BookingConfirmationMailer $confirmationMailer)
    {
    }

    public function checkout(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->load('service', 'package', 'items', 'cityPaymentSetting', 'city');
        $key = $booking->cityPaymentSetting?->gatewayKey() ?: config('services.razorpay.key');
        $secret = $booking->cityPaymentSetting?->gatewaySecret() ?: config('services.razorpay.secret');

        return view('booking.checkout', [
            'metaTitle' => 'Complete Payment',
            'metaDescription' => 'Complete booking payment with Razorpay.',
            'booking' => $booking,
            'razorpayConfigured' => filled($key) && filled($secret),
        ]);
    }

    public function createOrder(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->loadMissing('cityPaymentSetting');
        $key = $booking->cityPaymentSetting?->gatewayKey() ?: config('services.razorpay.key');
        $secret = $booking->cityPaymentSetting?->gatewaySecret() ?: config('services.razorpay.secret');

        if (! $key || ! $secret) {
            return response()->json(['message' => 'Razorpay keys are not configured.'], 422);
        }

        $amount = (int) round(((float) $booking->payable_amount) * 100);

        try {
            $order = $this->api($key, $secret)->order->create([
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => $booking->booking_no,
                'notes' => [
                    'booking_id' => $booking->id,
                    'customer' => $booking->customer_name,
                    'city' => $booking->cityPaymentSetting?->city,
                ],
            ]);
            $orderPayload = method_exists($order, 'toArray') ? $order->toArray() : iterator_to_array($order);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Unable to create Razorpay order.'], 422);
        }

        $orderId = $orderPayload['id'] ?? null;

        if (! $orderId) {
            return response()->json(['message' => 'Razorpay did not return an order id.'], 422);
        }

        Payment::updateOrCreate(
            ['booking_id' => $booking->id, 'razorpay_order_id' => $orderId],
            [
                'amount' => $booking->payable_amount,
                'currency' => 'INR',
                'status' => 'created',
                'payment_status' => 'Pending',
                'method' => 'razorpay',
                'raw_response' => $orderPayload,
            ]
        );

        return response()->json([
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'INR',
            'key' => $key,
            'name' => config('app.name'),
            'description' => $booking->item_title,
        ]);
    }

    public function verify(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $booking->loadMissing('cityPaymentSetting');
        $secret = $booking->cityPaymentSetting?->gatewaySecret() ?: config('services.razorpay.secret');

        if (! $secret) {
            return response()->json(['message' => 'Payment gateway is not configured for this city.'], 422);
        }

        try {
            $this->api($booking->cityPaymentSetting?->gatewayKey() ?: config('services.razorpay.key') ?: 'rzp_webhook', $secret)
                ->utility
                ->verifyPaymentSignature($validated);
        } catch (SignatureVerificationError $exception) {
            Payment::where('booking_id', $booking->id)
                ->where('razorpay_order_id', $validated['razorpay_order_id'])
                ->latest()
                ->first()
                ?->update(['status' => 'failed', 'payment_status' => 'Failed', 'raw_response' => $validated]);

            $booking->update(['payment_status' => 'Failed', 'workflow_status' => 'Payment Pending']);

            return response()->json(['message' => 'Payment signature verification failed.'], 422);
        }

        $payment = Payment::where('booking_id', $booking->id)
            ->where('razorpay_order_id', $validated['razorpay_order_id'])
            ->latest()
            ->first();

        if (! $payment) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'amount' => $booking->payable_amount,
                'currency' => 'INR',
                'method' => 'razorpay',
            ]);
        }

        $payment->update([
                'payment_id' => $validated['razorpay_payment_id'],
                'signature' => $validated['razorpay_signature'],
                'status' => 'paid',
                'payment_status' => 'Paid',
                'raw_response' => $validated,
        ]);

        $this->markBookingPaid($booking);

        return response()->json(['redirect' => route('payments.success', $booking)]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature');
        $event = json_decode($payload, true);

        if (! $signature || ! is_array($event)) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $orderId = data_get($event, 'payload.payment.entity.order_id') ?: data_get($event, 'payload.order.entity.id');
        $paymentId = data_get($event, 'payload.payment.entity.id');
        $bookingId = data_get($event, 'payload.payment.entity.notes.booking_id');

        $payment = $orderId ? Payment::where('razorpay_order_id', $orderId)->latest()->first() : null;
        $booking = $payment?->booking ?: ($bookingId ? Booking::find($bookingId) : null);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        $booking->loadMissing('cityPaymentSetting');
        $webhookSecret = $booking->cityPaymentSetting?->webhookSecret() ?: config('services.razorpay.webhook_secret');

        if (! $webhookSecret) {
            return response()->json(['message' => 'Razorpay webhook secret is not configured.'], 422);
        }

        try {
            $this->api($booking->cityPaymentSetting?->gatewayKey() ?: config('services.razorpay.key') ?: 'rzp_webhook', $booking->cityPaymentSetting?->gatewaySecret() ?: config('services.razorpay.secret') ?: 'rzp_webhook')
                ->utility
                ->verifyWebhookSignature($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationError $exception) {
            return response()->json(['message' => 'Webhook signature verification failed.'], 422);
        }

        if (! in_array($event['event'] ?? '', ['payment.captured', 'order.paid'], true)) {
            return response()->json(['status' => 'ignored']);
        }

        $payment ??= Payment::create([
            'booking_id' => $booking->id,
            'razorpay_order_id' => $orderId,
            'amount' => ((float) data_get($event, 'payload.payment.entity.amount', $booking->payable_amount * 100)) / 100,
            'currency' => data_get($event, 'payload.payment.entity.currency', 'INR'),
            'method' => 'razorpay',
        ]);

        $payment->update([
            'payment_id' => $paymentId ?: $payment->payment_id,
            'status' => 'paid',
            'payment_status' => 'Paid',
            'raw_response' => $event,
        ]);

        $this->markBookingPaid($booking);

        return response()->json(['status' => 'ok']);
    }

    public function success(Booking $booking)
    {
        $this->authorizeBooking($booking);
        return view('booking.success', ['metaTitle' => 'Booking Confirmed | Kids Party Planner', 'booking' => $booking->load(['items', 'city', 'latestPayment'])]);
    }

    public function failed(Booking $booking)
    {
        $this->authorizeBooking($booking);
        return view('booking.failed', ['metaTitle' => 'Payment Failed | Kids Party Planner', 'booking' => $booking]);
    }

    private function authorizeBooking(Booking $booking): void
    {
        abort_unless(auth()->id() === $booking->user_id || auth()->user()?->isAdmin(), 403);
    }

    private function api(string $key, string $secret): Api
    {
        return new Api($key, $secret);
    }

    private function markBookingPaid(Booking $booking): void
    {
        $booking->update([
            'status' => 'Confirmed',
            'workflow_status' => 'Confirmed',
            'payment_status' => $booking->payment_type === 'full' ? 'Paid' : 'Partially Paid',
            'tracking_status' => 'Payment Received',
        ]);

        try {
            $this->confirmationMailer->send($booking->refresh());
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}

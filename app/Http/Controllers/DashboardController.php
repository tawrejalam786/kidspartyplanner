<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailChange;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookingsQuery = $user->bookings();

        return view('dashboard.index', [
            'metaTitle' => 'Customer Dashboard',
            'bookings' => (clone $bookingsQuery)->with(['service', 'package', 'items', 'latestPayment', 'city'])->latest()->paginate(10),
            'nextBooking' => (clone $bookingsQuery)->with(['service', 'package', 'items', 'city'])->whereNotIn('workflow_status', ['Completed', 'Cancelled', 'Refunded'])->whereDate('event_date', '>=', today())->orderBy('event_date')->first(),
            'todayBookings' => (clone $bookingsQuery)->with(['service', 'package', 'city'])->whereNotIn('workflow_status', ['Completed', 'Cancelled', 'Refunded'])->whereDate('event_date', today())->orderBy('event_time')->get(),
            'stats' => [
                'total' => (clone $bookingsQuery)->count(),
                'upcoming' => (clone $bookingsQuery)->whereNotIn('workflow_status', ['Completed', 'Cancelled', 'Refunded'])->whereDate('event_date', '>=', today())->count(),
                'pending_payment' => (clone $bookingsQuery)->where('payment_status', 'Pending')->count(),
                'paid' => Payment::where('payment_status', 'Paid')->whereHas('booking', fn ($query) => $query->where('user_id', $user->id))->sum('amount'),
            ],
        ]);
    }

    public function booking(Booking $booking)
    {
        abort_unless(auth()->id() === $booking->user_id || auth()->user()?->isAdmin(), 403);

        return view('dashboard.booking', [
            'metaTitle' => 'Booking '.$booking->booking_no,
            'booking' => $booking->load(['service', 'package', 'items.addons.addon', 'bookingAddons.addon', 'payments', 'cityPaymentSetting', 'city', 'area', 'refunds']),
        ]);
    }

    public function payments(Request $request)
    {
        return view('dashboard.payments', [
            'metaTitle' => 'Payment History | Kids Party Planner',
            'payments' => Payment::with('booking')->whereHas('booking', fn ($query) => $query->where('user_id', $request->user()->id))->latest()->paginate(15),
        ]);
    }

    public function profile(Request $request)
    {
        return view('dashboard.profile', ['metaTitle' => 'My Profile | Kids Party Planner', 'user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $avatarPath = $user->avatar;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            if ($user->avatar && ! Str::startsWith($user->avatar, ['http://', 'https://'])) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'avatar' => $avatarPath,
        ]);

        $newEmail = Str::lower($validated['email']);
        $currentEmail = Str::lower($user->email);
        $message = 'Profile updated.';

        if ($newEmail !== $currentEmail) {
            $this->sendEmailChangeVerification($user, $newEmail);
            $message = 'Profile updated. Verification link sent to '.$newEmail.'.';
        } else {
            $user->forceFill([
                'pending_email' => null,
                'email_change_token' => null,
                'email_change_requested_at' => null,
            ]);
        }

        $user->save();

        return redirect()->route('dashboard.profile')->with('success', $message);
    }

    public function verifyProfileEmail(Request $request, string $token)
    {
        $user = $request->user();
        $hashed = hash('sha256', $token);

        if (! $user->pending_email || ! hash_equals((string) $user->email_change_token, $hashed)) {
            return redirect()->route('dashboard.profile')->withErrors(['email' => 'This email verification link is no longer valid.']);
        }

        if ($user->email_change_requested_at?->lt(now()->subDay())) {
            return redirect()->route('dashboard.profile')->withErrors(['email' => 'This email verification link has expired. Please request a new one.']);
        }

        if (User::where('email', $user->pending_email)->whereKeyNot($user->id)->exists()) {
            return redirect()->route('dashboard.profile')->withErrors(['email' => 'That email is already used by another account.']);
        }

        $user->forceFill([
            'email' => $user->pending_email,
            'email_verified_at' => now(),
            'pending_email' => null,
            'email_change_token' => null,
            'email_change_requested_at' => null,
        ])->save();

        return redirect()->route('dashboard.profile')->with('success', 'Email verified and updated.');
    }

    public function resendProfileEmailVerification(Request $request)
    {
        $user = $request->user();

        if (! $user->pending_email) {
            return back()->withErrors(['email' => 'No pending email change found.']);
        }

        $this->sendEmailChangeVerification($user, $user->pending_email);
        $user->save();

        return back()->with('success', 'Verification link sent again to '.$user->pending_email.'.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_unless($request->user()->id === $booking->user_id, 403);
        abort_if(in_array($booking->workflow_status, ['Completed', 'Cancelled', 'Refunded'], true), 422, 'This booking can no longer be cancelled.');
        $validated = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $paid = $booking->payments()->where('payment_status', 'Paid')->sum('amount');

        $booking->update([
            'status' => 'Cancelled',
            'workflow_status' => $paid > 0 ? 'Refund Requested' : 'Cancelled',
            'tracking_status' => $paid > 0 ? 'Refund Requested' : 'Cancelled',
            'cancellation_reason' => $validated['reason'],
            'cancelled_at' => now(),
        ]);

        if ($paid > 0) {
            Refund::firstOrCreate(['booking_id' => $booking->id, 'status' => 'Requested'], ['payment_id' => $booking->latestPayment?->id, 'amount' => $paid, 'reason' => $validated['reason']]);
        }

        return back()->with('success', $paid > 0 ? 'Cancellation and refund request submitted.' : 'Booking cancellation requested.');
    }

    public function rebook(Request $request, Booking $booking)
    {
        abort_unless($request->user()->id === $booking->user_id, 403);
        return $booking->service
            ? redirect()->route('booking.create', ['service' => $booking->service->slug])
            : ($booking->package ? redirect()->route('booking.create', ['package' => $booking->package->slug]) : redirect()->route('cart.index'));
    }

    public function invoice(Request $request, Booking $booking)
    {
        abort_unless($request->user()->id === $booking->user_id || $request->user()->isAdmin(), 403);
        $booking->load(['items.addons.addon', 'bookingAddons.addon', 'service', 'package', 'city', 'latestPayment']);
        $view = view('dashboard.invoice', compact('booking'));

        return $request->boolean('download')
            ? response($view->render())->header('Content-Type', 'text/html')->header('Content-Disposition', 'attachment; filename="'.$booking->invoice_no.'.html"')
            : $view;
    }

    private function sendEmailChangeVerification(User $user, string $newEmail): void
    {
        $token = Str::random(64);
        $user->forceFill([
            'pending_email' => $newEmail,
            'email_change_token' => hash('sha256', $token),
            'email_change_requested_at' => now(),
        ]);

        Mail::to($newEmail)->send(new VerifyEmailChange($user, URL::temporarySignedRoute(
            'dashboard.profile.email.verify',
            now()->addDay(),
            ['token' => $token],
        )));
    }
}

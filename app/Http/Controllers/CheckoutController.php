<?php

namespace App\Http\Controllers;

use App\Mail\BookingReceived;
use App\Models\Area;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\City;
use App\Models\CityPaymentSetting;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->cart($request)->load(['city', 'items.service.images', 'items.service.addons', 'items.package']);
        abort_if($cart->items->isEmpty(), 404, 'Your cart is empty.');
        $cities = City::where('is_current', true)->where('is_active', true)->with(['areas' => fn ($query) => $query->where('is_active', true)->orderBy('name')])->orderBy('sort_order')->get();

        return view('checkout.index', [
            'metaTitle' => 'Checkout | Kids Party Planner',
            'metaDescription' => 'Confirm event details and pay securely for your Kids Party Planner booking.',
            'cart' => $cart,
            'cities' => $cities,
            'paymentSettings' => CityPaymentSetting::where('is_active', true)->get()->keyBy('slug'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['required', 'email', 'max:160'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required'],
            'city_id' => ['required', 'exists:cities,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_name' => ['nullable', 'string', 'max:120'],
            'full_address' => ['required', 'string', 'max:1000'],
            'landmark' => ['nullable', 'string', 'max:180'],
            'event_type' => ['required', 'string', 'max:100'],
            'number_of_kids' => ['required', 'integer', 'min:1', 'max:500'],
            'age_group' => ['required', 'string', 'max:80'],
            'venue_type' => ['required', 'in:Home,Banquet,Society,School,Outdoor'],
            'decoration_theme' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string', 'max:2000'],
            'payment_type' => ['required', 'in:advance,full'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        $cart = $this->cart($request)->load(['items.service.cityPrices', 'items.package']);
        abort_if($cart->items->isEmpty(), 422, 'Your cart is empty.');
        $city = City::where('is_current', true)->where('is_active', true)->findOrFail($validated['city_id']);
        $area = ! empty($validated['area_id']) ? Area::where('city_id', $city->id)->findOrFail($validated['area_id']) : null;
        $paymentSetting = CityPaymentSetting::where('slug', $city->slug)->where('is_active', true)->firstOrFail();

        $lines = $cart->items->map(function ($item) use ($city) {
            $unitPrice = $item->service ? $item->service->priceForCity($city) : $item->package->effective_price;
            $addonsTotal = collect($item->selected_addons ?? [])->sum(fn ($addon) => (float) ($addon['price'] ?? 0) * (int) ($addon['quantity'] ?? 1));
            return ['cart_item' => $item, 'unit_price' => $unitPrice, 'line_total' => ($unitPrice * $item->quantity) + $addonsTotal];
        });

        $baseAmount = (float) $lines->sum('line_total');
        $coupon = null;
        $discount = 0;
        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', Str::upper($validated['coupon_code']))->first();
            if (! $coupon || ! $coupon->isValidFor($baseAmount)) {
                return back()->withErrors(['coupon_code' => 'This coupon is not valid for your cart.'])->withInput();
            }
            $discount = $coupon->discountFor($baseAmount);
        }

        $serviceFee = (float) $paymentSetting->service_fee + (float) ($area?->travel_fee ?? 0);
        $taxAmount = round((max(0, $baseAmount - $discount) + $serviceFee) * ((float) $paymentSetting->tax_percent / 100), 2);
        $total = round(max(0, $baseAmount - $discount) + $serviceFee + $taxAmount, 2);
        $advance = min($total, max((float) $paymentSetting->minimum_advance, round($total * ((float) $paymentSetting->advance_percent / 100), 2)));
        $payable = $validated['payment_type'] === 'full' ? $total : $advance;

        $booking = DB::transaction(function () use ($request, $validated, $cart, $city, $area, $paymentSetting, $lines, $baseAmount, $discount, $serviceFee, $taxAmount, $total, $advance, $payable, $coupon) {
            $first = $cart->items->first();
            $booking = Booking::create([
                ...$validated,
                'booking_no' => 'KPP-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'invoice_no' => 'INV-'.now()->format('ym').'-'.Str::upper(Str::random(6)),
                'user_id' => $request->user()->id,
                'service_id' => $cart->items->count() === 1 ? $first->service_id : null,
                'package_id' => $cart->items->count() === 1 ? $first->package_id : null,
                'city_payment_setting_id' => $paymentSetting->id,
                'city_id' => $city->id,
                'area_id' => $area?->id,
                'area_name' => $area?->name ?: ($validated['area_name'] ?? null),
                'location' => $validated['full_address'],
                'status' => 'Pending',
                'workflow_status' => 'Payment Pending',
                'payment_status' => 'Pending',
                'tracking_status' => 'Booking Placed',
                'base_amount' => $baseAmount,
                'service_fee' => $serviceFee,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'advance_amount' => $advance,
                'payable_amount' => $payable,
                'coupon_code' => $coupon?->code,
                'coupon_discount' => $discount,
                'add_ons' => $cart->items
                    ->flatMap(fn ($item) => $item->selected_addons ?? [])
                    ->filter()
                    ->values()
                    ->all(),
            ]);

            foreach ($lines as $line) {
                $cartItem = $line['cart_item'];
                $bookingItem = $booking->items()->create([
                    'service_id' => $cartItem->service_id,
                    'package_id' => $cartItem->package_id,
                    'item_name' => $cartItem->title,
                    'item_type' => $cartItem->service_id ? 'service' : 'package',
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                ]);
                foreach ($cartItem->selected_addons ?? [] as $addon) {
                    $booking->bookingAddons()->create(['booking_item_id' => $bookingItem->id, 'addon_id' => $addon['id'] ?? null, 'name' => $addon['name'], 'price' => $addon['price'], 'quantity' => $addon['quantity'] ?? 1]);
                }
            }

            $cart->delete();
            $coupon?->increment('used_count');
            return $booking;
        });

        try {
            Mail::to(Setting::getValue('admin_email', config('mail.from.address')))->send(new BookingReceived($booking));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('payments.checkout', $booking)->with('success', 'Booking created. Complete payment to confirm your slot.');
    }

    private function cart(Request $request): Cart
    {
        return Cart::where('user_id', $request->user()->id)->latest()->firstOrFail();
    }
}

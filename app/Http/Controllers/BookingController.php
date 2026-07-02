<?php

namespace App\Http\Controllers;

use App\Mail\BookingReceived;
use App\Models\Booking;
use App\Models\CityPaymentSetting;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Package as PartyPackage;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $service = $request->filled('service') ? Service::with(['cityPrices', 'addons'])->where('slug', $request->get('service'))->firstOrFail() : null;
        $package = $request->filled('package') ? PartyPackage::where('slug', $request->get('package'))->firstOrFail() : null;

        $services = Service::where('is_active', true)->orderBy('title')->get();
        $packages = PartyPackage::where('is_active', true)->orderBy('title')->get();
        $cities = CityPaymentSetting::where('is_active', true)->orderByDesc('is_default')->orderBy('city')->get();

        return view('booking.create', [
            'metaTitle' => 'Book a Kids Party Service',
            'metaDescription' => 'Book birthday party services and packages for Delhi NCR.',
            'service' => $service,
            'package' => $package,
            'services' => $services,
            'packages' => $packages,
            'cities' => $cities,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'city_payment_setting_id' => ['required', 'exists:city_payment_settings,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required'],
            'location' => ['nullable', 'string', 'max:255'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_name' => ['nullable', 'string', 'max:120'],
            'full_address' => ['required', 'string', 'max:1000'],
            'landmark' => ['nullable', 'string', 'max:180'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'age_group' => ['nullable', 'string', 'max:80'],
            'venue_type' => ['nullable', 'in:Home,Banquet,Society,School,Outdoor'],
            'decoration_theme' => ['nullable', 'string', 'max:180'],
            'number_of_kids' => ['required', 'integer', 'min:1', 'max:500'],
            'add_ons' => ['nullable', 'array'],
            'message' => ['nullable', 'string', 'max:2000'],
            'payment_type' => ['required', 'in:advance,full'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($validated['service_id']) && empty($validated['package_id'])) {
            return back()->withErrors(['service_id' => 'Please select a service or package.'])->withInput();
        }

        $service = ! empty($validated['service_id']) ? Service::with(['cityPrices', 'addons'])->findOrFail($validated['service_id']) : null;
        $package = ! empty($validated['package_id']) ? PartyPackage::findOrFail($validated['package_id']) : null;
        $cityPayment = CityPaymentSetting::where('is_active', true)->findOrFail($validated['city_payment_setting_id']);
        $city = City::where('slug', $cityPayment->slug)->where('is_active', true)->first();
        $item = $service ?: $package;
        $selectedAddOns = $this->selectedAddOns($service, $validated['add_ons'] ?? []);
        $itemPrice = $service ? $service->priceForCity($city) : (float) $package->effective_price;
        $baseAmount = $itemPrice + collect($selectedAddOns)->sum('price');
        $couponDiscount = 0;
        $coupon = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', Str::upper($validated['coupon_code']))->first();

            if (! $coupon || ! $coupon->isValidFor($baseAmount)) {
                return back()->withErrors(['coupon_code' => 'This coupon is not valid for the selected booking.'])->withInput();
            }

            $couponDiscount = $coupon->discountFor($baseAmount);
        }

        $discountedSubtotal = max(0, $baseAmount - $couponDiscount);
        $serviceFee = (float) $cityPayment->service_fee;
        $taxAmount = round(($discountedSubtotal + $serviceFee) * ((float) $cityPayment->tax_percent / 100), 2);
        $total = round($discountedSubtotal + $serviceFee + $taxAmount, 2);
        $advanceAmount = min($total, max(
            (float) $cityPayment->minimum_advance,
            round($total * ((float) $cityPayment->advance_percent / 100), 2)
        ));
        $payableAmount = match ($validated['payment_type']) {
            'full' => $total,
            default => $advanceAmount,
        };

        $booking = Booking::create([
            ...$validated,
            'booking_no' => 'KPP-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'user_id' => $request->user()->id,
            'city_id' => $city?->id,
            'invoice_no' => 'INV-'.now()->format('ym').'-'.Str::upper(Str::random(6)),
            'location' => $validated['full_address'],
            'full_address' => $validated['full_address'],
            'add_ons' => $selectedAddOns,
            'workflow_status' => 'Payment Pending',
            'payment_status' => 'Pending',
            'tracking_status' => 'Booking Placed',
            'base_amount' => $baseAmount,
            'service_fee' => $serviceFee,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'advance_amount' => $advanceAmount,
            'payable_amount' => $payableAmount,
            'coupon_code' => $coupon?->code,
            'coupon_discount' => $couponDiscount,
        ]);

        $bookingItem = $booking->items()->create([
            'service_id' => $service?->id,
            'package_id' => $package?->id,
            'item_name' => $item->title,
            'item_type' => $service ? 'service' : 'package',
            'quantity' => 1,
            'unit_price' => $itemPrice,
            'line_total' => $baseAmount,
        ]);
        foreach ($selectedAddOns as $addon) {
            $booking->bookingAddons()->create(['booking_item_id' => $bookingItem->id, 'addon_id' => $addon['id'] ?? null, 'name' => $addon['name'], 'price' => $addon['price'], 'quantity' => 1]);
        }

        if ($coupon) {
            $coupon->increment('used_count');
        }

        try {
            Mail::to(Setting::getValue('admin_email', config('mail.from.address')))
                ->send(new BookingReceived($booking));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('payments.checkout', $booking)->with('success', 'Booking created. You can complete payment now.');
    }

    private function selectedAddOns(?Service $service, array $requested): array
    {
        if (! $service || empty($requested)) {
            return [];
        }

        $legacy = collect($service->add_ons ?? [])->map(fn ($addon) => [
            ...$addon,
            'image' => $addon['image'] ?? \App\Models\Addon::fallbackImageUrl(),
        ]);
        $managed = $service->relationLoaded('addons')
            ? $service->addons->map(fn ($addon) => ['id' => $addon->id, 'name' => $addon->name, 'price' => (float) ($addon->pivot->price_override ?: $addon->price), 'image' => $addon->image_url])
            : collect();
        $allowed = $legacy->concat($managed)->keyBy('name');

        return collect($requested)
            ->filter(fn ($name) => $allowed->has($name))
            ->map(fn ($name) => $allowed[$name])
            ->values()
            ->all();
    }
}

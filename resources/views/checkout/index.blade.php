@extends('layouts.app')

@section('content')
<section class="checkout-progress">
    <div class="container">
        <span class="done"><i class="fa-solid fa-check"></i> Cart</span>
        <i class="fa-solid fa-chevron-right"></i>
        <span class="active">Event details</span>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Payment</span>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Confirmation</span>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <form method="post" action="{{ route('checkout.store') }}" class="checkout-form">
            @csrf
            <div class="row g-4 align-items-start">
                <div class="col-xl-8">
                    <div class="checkout-panel">
                        <div class="booking-step-head">
                            <span>01</span>
                            <div><strong>Contact details</strong><small>Used for confirmations and event coordination</small></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer name</label>
                                <input class="form-control" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input class="form-control" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" required>
                            </div>
                        </div>

                        <div class="booking-divider"></div>
                        <div class="booking-step-head">
                            <span>02</span>
                            <div><strong>Event schedule & location</strong><small>City selection updates service pricing</small></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Event date</label>
                                <input class="form-control" type="date" name="event_date" min="{{ date('Y-m-d') }}" value="{{ old('event_date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event time</label>
                                <input class="form-control" type="time" name="event_time" value="{{ old('event_time') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <select class="form-select" name="city_id" data-checkout-city required>
                                    @foreach($cities as $city)
                                        <option
                                            value="{{ $city->id }}"
                                            data-slug="{{ $city->slug }}"
                                            data-fee="{{ $paymentSettings[$city->slug]?->service_fee ?? 0 }}"
                                            data-tax="{{ $paymentSettings[$city->slug]?->tax_percent ?? 0 }}"
                                            data-advance="{{ $paymentSettings[$city->slug]?->advance_percent ?? 30 }}"
                                            data-minimum="{{ $paymentSettings[$city->slug]?->minimum_advance ?? 0 }}"
                                            @selected(old('city_id', $cart->city_id ?: $selectedCity?->id) == $city->id)
                                        >{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Area / locality</label>
                                <select class="form-select" name="area_id" data-checkout-area>
                                    <option value="">Choose area</option>
                                    @foreach($cities as $city)
                                        @foreach($city->areas as $area)
                                            <option value="{{ $area->id }}" data-city="{{ $city->id }}" data-travel="{{ $area->travel_fee }}" @selected(old('area_id') == $area->id)>{{ $area->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full address</label>
                                <textarea class="form-control" name="full_address" rows="2" required>{{ old('full_address', auth()->user()->address) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Landmark</label>
                                <input class="form-control" name="landmark" value="{{ old('landmark') }}">
                            </div>
                        </div>

                        <div class="booking-divider"></div>
                        <div class="booking-step-head">
                            <span>03</span>
                            <div><strong>Party details</strong><small>Helps us send the right team and materials</small></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Event type</label>
                                <select class="form-select" name="event_type" required>
                                    @foreach(['Kids Birthday', 'Birthday Decoration', 'Anniversary', 'Welcome Baby', 'Naming Ceremony', 'Other'] as $eventType)
                                        <option @selected(old('event_type') === $eventType)>{{ $eventType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Venue type</label>
                                <select class="form-select" name="venue_type" required>
                                    @foreach(['Home', 'Banquet', 'Society', 'School', 'Outdoor'] as $venue)
                                        <option @selected(old('venue_type') === $venue)>{{ $venue }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of kids</label>
                                <input class="form-control" type="number" name="number_of_kids" min="1" max="500" value="{{ old('number_of_kids', 15) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Age group</label>
                                <select class="form-select" name="age_group" required>
                                    @foreach(['1-3 years', '4-6 years', '7-10 years', '11-14 years', 'Mixed age group'] as $age)
                                        <option @selected(old('age_group') === $age)>{{ $age }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Decoration theme</label>
                                <input class="form-control" name="decoration_theme" value="{{ old('decoration_theme') }}" placeholder="Optional: theme, colors or character">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Special instructions</label>
                                <textarea class="form-control" name="message" rows="3">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        <div class="booking-divider"></div>
                        <div class="booking-step-head">
                            <span>04</span>
                            <div><strong>Payment preference</strong><small>Pay advance or complete the payment</small></div>
                        </div>
                        <div class="payment-choice-grid">
                            <label class="payment-choice">
                                <input type="radio" name="payment_type" value="advance" @checked(old('payment_type', 'advance') === 'advance')>
                                <span><i class="fa-solid fa-calendar-check"></i><strong>Pay advance</strong><small>Reserve your date</small></span>
                            </label>
                            <label class="payment-choice">
                                <input type="radio" name="payment_type" value="full" @checked(old('payment_type') === 'full')>
                                <span><i class="fa-solid fa-shield-halved"></i><strong>Pay in full</strong><small>Finish payment now</small></span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Coupon code</label>
                            <input class="form-control" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="PARTY10">
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <aside class="checkout-summary" data-checkout-summary>
                        <span class="mini-label">Booking summary</span>
                        <h2>{{ $cart->items->sum('quantity') }} selected item{{ $cart->items->sum('quantity') === 1 ? '' : 's' }}</h2>
                        <div class="checkout-items">
                            @foreach($cart->items as $item)
                                @php
                                    $prices = $item->service
                                        ? $item->service->cityPrices->mapWithKeys(fn ($price) => [$price->city_id => $price->effective_price])->all()
                                        : [];
                                    $addonsTotal = collect($item->selected_addons ?? [])->sum(
                                        fn ($addon) => (float) $addon['price'] * (int) ($addon['quantity'] ?? 1)
                                    );
                                @endphp
                                <div
                                    class="checkout-item"
                                    data-quantity="{{ $item->quantity }}"
                                    data-default-price="{{ $item->unit_price }}"
                                    data-prices='@json($prices)'
                                    data-addons="{{ $addonsTotal }}"
                                >
                                    <img src="{{ $item->service?->image_url ?? $item->package?->image_url }}" alt="{{ $item->title }}" loading="lazy">
                                    <span><strong>{{ $item->title }}</strong><small>Qty {{ $item->quantity }}</small></span>
                                    <b>&#8377;{{ number_format($item->line_total) }}</b>
                                </div>
                                @if($item->selected_addons)
                                    <div class="checkout-addon-list">
                                        @foreach($item->selected_addons as $addon)
                                            <div><img src="{{ $addon['image'] ?? \App\Models\Addon::fallbackImageUrl() }}" alt="{{ $addon['name'] }}"><span><strong>{{ $addon['name'] }}</strong><small>Add-on</small></span><b>+&#8377;{{ number_format((float) $addon['price']) }}</b></div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="summary-lines">
                            <div><span>Items subtotal</span><strong data-checkout-subtotal>&#8377;{{ number_format($cart->subtotal) }}</strong></div>
                            <div><span>City & travel fee</span><strong data-checkout-fee>&#8377;0</strong></div>
                            <div><span>Estimated tax</span><strong data-checkout-tax>&#8377;0</strong></div>
                            <div class="summary-total"><span>Total estimate</span><strong data-checkout-total>&#8377;{{ number_format($cart->subtotal) }}</strong></div>
                            <div class="summary-due"><span>Due now</span><strong data-checkout-due>&#8377;0</strong></div>
                        </div>
                        <button class="btn btn-party btn-lg w-100" type="submit"><i class="fa-solid fa-lock"></i> Continue to secure payment</button>
                        <p class="secure-note"><i class="fa-solid fa-shield-halved"></i> Razorpay-secured checkout. Final totals are validated by the server.</p>
                    </aside>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@auth
    @php
        $fixedPrice = (float) (($service ?? null)?->priceForCity($selectedCity ?? null) ?? ($package ?? null)?->effective_price ?? 0);
        $legacyAddons = collect($service->add_ons ?? [])->map(fn ($addon) => [...$addon, 'image' => $addon['image'] ?? \App\Models\Addon::fallbackImageUrl()]);
        $managedAddons = isset($service) && $service ? $service->addons->map(fn ($addon) => ['name' => $addon->name, 'price' => (float) ($addon->pivot->price_override ?: $addon->price), 'image' => $addon->image_url]) : collect();
        $availableAddons = isset($service) && $service ? $legacyAddons->concat($managedAddons)->unique('name')->values() : collect();
        $selectedCityId = old('city_payment_setting_id');
        if (!$selectedCityId && auth()->user()->city) {
            $selectedCityId = optional($cities->first(fn ($city) => strcasecmp($city->city, auth()->user()->city) === 0))->id;
        }
        $selectedCityId = $selectedCityId ?: optional($cities->firstWhere('is_default', true))->id ?: optional($cities->first())->id;
    @endphp
    <form action="{{ route('booking.store') }}" method="post" class="booking-form" data-base-price="{{ $fixedPrice }}">
        @csrf
        <div class="booking-step-head"><span>01</span><div><strong>Choose your experience</strong><small>One service or one complete package</small></div></div>
        @if(isset($service) && $service)
            <input type="hidden" name="service_id" value="{{ $service->id }}">
            <div class="selected-booking-item"><i class="fa-solid fa-wand-magic-sparkles"></i><div><strong>{{ $service->title }}</strong><span>From &#8377;{{ number_format($fixedPrice) }} in {{ $selectedCity?->name }}</span></div></div>
        @elseif(isset($package) && $package)
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            <div class="selected-booking-item"><i class="fa-solid fa-box-open"></i><div><strong>{{ $package->title }}</strong><span>From &#8377;{{ number_format($package->effective_price) }}</span></div></div>
        @else
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-select" data-booking-service>
                        <option value="" data-price="0">Select service</option>
                        @foreach($services ?? [] as $option)
                            <option value="{{ $option->id }}" data-price="{{ $option->effective_price }}" @selected(old('service_id') == $option->id)>{{ $option->title }} - &#8377;{{ number_format($option->effective_price) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Package</label>
                    <select name="package_id" class="form-select" data-booking-package>
                        <option value="" data-price="0">Select package</option>
                        @foreach($packages ?? [] as $option)
                            <option value="{{ $option->id }}" data-price="{{ $option->effective_price }}" @selected(old('package_id') == $option->id)>{{ $option->title }} - &#8377;{{ number_format($option->effective_price) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="booking-divider"></div>
        <div class="booking-step-head"><span>02</span><div><strong>Party details</strong><small>Location, schedule and guest count</small></div></div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Parent name</label>
                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', auth()->user()->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', auth()->user()->phone) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', auth()->user()->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <select name="city_payment_setting_id" class="form-select" data-city-payment required>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}"
                                data-city="{{ $city->city }}"
                                data-city-id="{{ optional($activeCities->firstWhere('slug', $city->slug))->id }}"
                                data-advance="{{ $city->advance_percent }}"
                                data-minimum="{{ $city->minimum_advance }}"
                                data-fee="{{ $city->service_fee }}"
                                data-tax="{{ $city->tax_percent }}"
                                data-note="{{ $city->payment_instructions }}"
                                @selected((string) $selectedCityId === (string) $city->id)>
                            {{ $city->city }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Area / locality</label>
                <select name="area_id" class="form-select" data-booking-area>
                    <option value="">Choose area</option>
                    @foreach($activeCities as $marketCity)
                        @foreach($marketCity->areas as $area)
                            <option value="{{ $area->id }}" data-city="{{ $marketCity->id }}" @selected(old('area_id') == $area->id)>{{ $area->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Event date</label>
                <input type="date" name="event_date" class="form-control" value="{{ old('event_date') }}" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start time</label>
                <input type="time" name="event_time" class="form-control" value="{{ old('event_time') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kids</label>
                <input type="number" name="number_of_kids" class="form-control" min="1" max="500" value="{{ old('number_of_kids', 15) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Full event address</label>
                <input type="text" name="full_address" class="form-control" value="{{ old('full_address', auth()->user()->address) }}" placeholder="House number, venue, sector, city and landmark" required>
            </div>
            <div class="col-md-6"><label class="form-label">Landmark</label><input class="form-control" name="landmark" value="{{ old('landmark') }}"></div>
            <div class="col-md-6"><label class="form-label">Venue type</label><select class="form-select" name="venue_type">@foreach(['Home','Banquet','Society','School','Outdoor'] as $venue)<option>{{ $venue }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Event type</label><select class="form-select" name="event_type"><option>Kids Birthday</option><option>Birthday Decoration</option><option>Anniversary</option><option>Welcome Baby</option><option>Other</option></select></div>
            <div class="col-md-6"><label class="form-label">Age group</label><select class="form-select" name="age_group">@foreach(['1-3 years','4-6 years','7-10 years','11-14 years','Mixed age group'] as $age)<option>{{ $age }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">Decoration theme</label><input class="form-control" name="decoration_theme" value="{{ old('decoration_theme') }}" placeholder="Optional theme or preferred colors"></div>
            @if($availableAddons->isNotEmpty())
                <div class="col-12">
                    <label class="form-label">Useful add-ons</label>
                    <div class="addon-grid">
                        @foreach($availableAddons as $addon)
                            <label class="addon-option">
                                <input type="checkbox" name="add_ons[]" value="{{ $addon['name'] }}" data-addon-price="{{ $addon['price'] }}" @checked(in_array($addon['name'], old('add_ons', [])))>
                                <img src="{{ $addon['image'] ?? \App\Models\Addon::fallbackImageUrl() }}" alt="{{ $addon['name'] }}" loading="lazy">
                                <span><b>{{ $addon['name'] }}</b><strong>+&#8377;{{ number_format($addon['price']) }}</strong></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="booking-divider"></div>
        <div class="booking-step-head"><span>03</span><div><strong>Payment preference</strong><small>The amount due now changes by city</small></div></div>
        <div class="payment-choice-grid">
            @foreach(['advance' => ['Pay advance', 'Reserve the date'], 'full' => ['Pay in full', 'Finish payment now']] as $value => $copy)
                <label class="payment-choice">
                    <input type="radio" name="payment_type" value="{{ $value }}" @checked(old('payment_type', 'advance') === $value)>
                    <span><i class="fa-solid {{ $value === 'advance' ? 'fa-calendar-check' : 'fa-shield-halved' }}"></i><strong>{{ $copy[0] }}</strong><small>{{ $copy[1] }}</small></span>
                </label>
            @endforeach
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-5">
                <label class="form-label">Coupon</label>
                <input type="text" name="coupon_code" class="form-control" value="{{ old('coupon_code') }}" placeholder="PARTY10">
            </div>
            <div class="col-md-7">
                <label class="form-label">Party notes</label>
                <input type="text" name="message" class="form-control" value="{{ old('message') }}" placeholder="Theme, child's age or venue notes">
            </div>
        </div>

        <div class="price-preview" aria-live="polite">
            <div><span>Service subtotal</span><strong data-preview-subtotal>&#8377;0</strong></div>
            <div><span>City convenience fee</span><strong data-preview-fee>&#8377;0</strong></div>
            <div><span>Estimated tax</span><strong data-preview-tax>&#8377;0</strong></div>
            <div class="price-preview-total"><span>Total estimate</span><strong data-preview-total>&#8377;0</strong></div>
            <div class="price-preview-due"><span>Due now</span><strong data-preview-due>&#8377;0</strong></div>
            <p data-city-note></p>
        </div>
        <button class="btn btn-party btn-lg w-100 mt-3" type="submit"><span>Continue to confirmation</span><i class="fa-solid fa-arrow-right"></i></button>
    </form>
@else
    <div class="login-card">
        <span class="mini-label">Customer account</span>
        <h3>Login to reserve this party</h3>
        <p>Your dashboard keeps booking, payment and event status together.</p>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-party" href="{{ route('login') }}">Login</a>
            <a class="btn btn-outline-party" href="{{ route('register') }}">Create account</a>
        </div>
    </div>
@endauth

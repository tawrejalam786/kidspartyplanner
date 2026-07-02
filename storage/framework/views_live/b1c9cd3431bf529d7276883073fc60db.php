<?php if(auth()->guard()->check()): ?>
    <?php
        $fixedPrice = (float) (($service ?? null)?->priceForCity($selectedCity ?? null) ?? ($package ?? null)?->effective_price ?? 0);
        $legacyAddons = collect($service->add_ons ?? [])->map(fn ($addon) => [...$addon, 'image' => $addon['image'] ?? \App\Models\Addon::fallbackImageUrl()]);
        $managedAddons = isset($service) && $service ? $service->addons->map(fn ($addon) => ['name' => $addon->name, 'price' => (float) ($addon->pivot->price_override ?: $addon->price), 'image' => $addon->image_url]) : collect();
        $availableAddons = isset($service) && $service ? $legacyAddons->concat($managedAddons)->unique('name')->values() : collect();
        $selectedCityId = old('city_payment_setting_id');
        if (!$selectedCityId && auth()->user()->city) {
            $selectedCityId = optional($cities->first(fn ($city) => strcasecmp($city->city, auth()->user()->city) === 0))->id;
        }
        $selectedCityId = $selectedCityId ?: optional($cities->firstWhere('is_default', true))->id ?: optional($cities->first())->id;
    ?>
    <form action="<?php echo e(route('booking.store')); ?>" method="post" class="booking-form" data-base-price="<?php echo e($fixedPrice); ?>">
        <?php echo csrf_field(); ?>
        <div class="booking-step-head"><span>01</span><div><strong>Choose your experience</strong><small>One service or one complete package</small></div></div>
        <?php if(isset($service) && $service): ?>
            <input type="hidden" name="service_id" value="<?php echo e($service->id); ?>">
            <div class="selected-booking-item"><i class="fa-solid fa-wand-magic-sparkles"></i><div><strong><?php echo e($service->title); ?></strong><span>From &#8377;<?php echo e(number_format($fixedPrice)); ?> in <?php echo e($selectedCity?->name); ?></span></div></div>
        <?php elseif(isset($package) && $package): ?>
            <input type="hidden" name="package_id" value="<?php echo e($package->id); ?>">
            <div class="selected-booking-item"><i class="fa-solid fa-box-open"></i><div><strong><?php echo e($package->title); ?></strong><span>From &#8377;<?php echo e(number_format($package->effective_price)); ?></span></div></div>
        <?php else: ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-select" data-booking-service>
                        <option value="" data-price="0">Select service</option>
                        <?php $__currentLoopData = $services ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option->id); ?>" data-price="<?php echo e($option->effective_price); ?>" <?php if(old('service_id') == $option->id): echo 'selected'; endif; ?>><?php echo e($option->title); ?> - &#8377;<?php echo e(number_format($option->effective_price)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Package</label>
                    <select name="package_id" class="form-select" data-booking-package>
                        <option value="" data-price="0">Select package</option>
                        <?php $__currentLoopData = $packages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option->id); ?>" data-price="<?php echo e($option->effective_price); ?>" <?php if(old('package_id') == $option->id): echo 'selected'; endif; ?>><?php echo e($option->title); ?> - &#8377;<?php echo e(number_format($option->effective_price)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="booking-divider"></div>
        <div class="booking-step-head"><span>02</span><div><strong>Party details</strong><small>Location, schedule and guest count</small></div></div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Parent name</label>
                <input type="text" name="customer_name" class="form-control" value="<?php echo e(old('customer_name', auth()->user()->name)); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="customer_phone" class="form-control" value="<?php echo e(old('customer_phone', auth()->user()->phone)); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="customer_email" class="form-control" value="<?php echo e(old('customer_email', auth()->user()->email)); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <select name="city_payment_setting_id" class="form-select" data-city-payment required>
                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($city->id); ?>"
                                data-city="<?php echo e($city->city); ?>"
                                data-city-id="<?php echo e(optional($activeCities->firstWhere('slug', $city->slug))->id); ?>"
                                data-advance="<?php echo e($city->advance_percent); ?>"
                                data-minimum="<?php echo e($city->minimum_advance); ?>"
                                data-fee="<?php echo e($city->service_fee); ?>"
                                data-tax="<?php echo e($city->tax_percent); ?>"
                                data-note="<?php echo e($city->payment_instructions); ?>"
                                <?php if((string) $selectedCityId === (string) $city->id): echo 'selected'; endif; ?>>
                            <?php echo e($city->city); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Area / locality</label>
                <select name="area_id" class="form-select" data-booking-area>
                    <option value="">Choose area</option>
                    <?php $__currentLoopData = $activeCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marketCity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $marketCity->areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($area->id); ?>" data-city="<?php echo e($marketCity->id); ?>" <?php if(old('area_id') == $area->id): echo 'selected'; endif; ?>><?php echo e($area->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Event date</label>
                <input type="date" name="event_date" class="form-control" value="<?php echo e(old('event_date')); ?>" min="<?php echo e(date('Y-m-d')); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start time</label>
                <input type="time" name="event_time" class="form-control" value="<?php echo e(old('event_time')); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kids</label>
                <input type="number" name="number_of_kids" class="form-control" min="1" max="500" value="<?php echo e(old('number_of_kids', 15)); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Full event address</label>
                <input type="text" name="full_address" class="form-control" value="<?php echo e(old('full_address', auth()->user()->address)); ?>" placeholder="House number, venue, sector, city and landmark" required>
            </div>
            <div class="col-md-6"><label class="form-label">Landmark</label><input class="form-control" name="landmark" value="<?php echo e(old('landmark')); ?>"></div>
            <div class="col-md-6"><label class="form-label">Venue type</label><select class="form-select" name="venue_type"><?php $__currentLoopData = ['Home','Banquet','Society','School','Outdoor']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($venue); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-6"><label class="form-label">Event type</label><select class="form-select" name="event_type"><option>Kids Birthday</option><option>Birthday Decoration</option><option>Anniversary</option><option>Welcome Baby</option><option>Other</option></select></div>
            <div class="col-md-6"><label class="form-label">Age group</label><select class="form-select" name="age_group"><?php $__currentLoopData = ['1-3 years','4-6 years','7-10 years','11-14 years','Mixed age group']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $age): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($age); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-12"><label class="form-label">Decoration theme</label><input class="form-control" name="decoration_theme" value="<?php echo e(old('decoration_theme')); ?>" placeholder="Optional theme or preferred colors"></div>
            <?php if($availableAddons->isNotEmpty()): ?>
                <div class="col-12">
                    <label class="form-label">Useful add-ons</label>
                    <div class="addon-grid">
                        <?php $__currentLoopData = $availableAddons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="addon-option">
                                <input type="checkbox" name="add_ons[]" value="<?php echo e($addon['name']); ?>" data-addon-price="<?php echo e($addon['price']); ?>" <?php if(in_array($addon['name'], old('add_ons', []))): echo 'checked'; endif; ?>>
                                <img src="<?php echo e($addon['image'] ?? \App\Models\Addon::fallbackImageUrl()); ?>" alt="<?php echo e($addon['name']); ?>" loading="lazy">
                                <span><b><?php echo e($addon['name']); ?></b><strong>+&#8377;<?php echo e(number_format($addon['price'])); ?></strong></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="booking-divider"></div>
        <div class="booking-step-head"><span>03</span><div><strong>Payment preference</strong><small>The amount due now changes by city</small></div></div>
        <div class="payment-choice-grid">
            <?php $__currentLoopData = ['advance' => ['Pay advance', 'Reserve the date'], 'full' => ['Pay in full', 'Finish payment now']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $copy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="payment-choice">
                    <input type="radio" name="payment_type" value="<?php echo e($value); ?>" <?php if(old('payment_type', 'advance') === $value): echo 'checked'; endif; ?>>
                    <span><i class="fa-solid <?php echo e($value === 'advance' ? 'fa-calendar-check' : 'fa-shield-halved'); ?>"></i><strong><?php echo e($copy[0]); ?></strong><small><?php echo e($copy[1]); ?></small></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-5">
                <label class="form-label">Coupon</label>
                <input type="text" name="coupon_code" class="form-control" value="<?php echo e(old('coupon_code')); ?>" placeholder="PARTY10">
            </div>
            <div class="col-md-7">
                <label class="form-label">Party notes</label>
                <input type="text" name="message" class="form-control" value="<?php echo e(old('message')); ?>" placeholder="Theme, child's age or venue notes">
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
<?php else: ?>
    <div class="login-card">
        <span class="mini-label">Customer account</span>
        <h3>Login to reserve this party</h3>
        <p>Your dashboard keeps booking, payment and event status together.</p>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-party" href="<?php echo e(route('login')); ?>">Login</a>
            <a class="btn btn-outline-party" href="<?php echo e(route('register')); ?>">Create account</a>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/partials/booking-form.blade.php ENDPATH**/ ?>
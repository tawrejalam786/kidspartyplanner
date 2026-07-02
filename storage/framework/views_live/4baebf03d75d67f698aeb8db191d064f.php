<?php $__env->startSection('content'); ?>
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
        <form method="post" action="<?php echo e(route('checkout.store')); ?>" class="checkout-form">
            <?php echo csrf_field(); ?>
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
                                <input class="form-control" name="customer_name" value="<?php echo e(old('customer_name', auth()->user()->name)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input class="form-control" name="customer_phone" value="<?php echo e(old('customer_phone', auth()->user()->phone)); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="customer_email" value="<?php echo e(old('customer_email', auth()->user()->email)); ?>" required>
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
                                <input class="form-control" type="date" name="event_date" min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(old('event_date')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event time</label>
                                <input class="form-control" type="time" name="event_time" value="<?php echo e(old('event_time')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <select class="form-select" name="city_id" data-checkout-city required>
                                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($city->id); ?>"
                                            data-slug="<?php echo e($city->slug); ?>"
                                            data-fee="<?php echo e($paymentSettings[$city->slug]?->service_fee ?? 0); ?>"
                                            data-tax="<?php echo e($paymentSettings[$city->slug]?->tax_percent ?? 0); ?>"
                                            data-advance="<?php echo e($paymentSettings[$city->slug]?->advance_percent ?? 30); ?>"
                                            data-minimum="<?php echo e($paymentSettings[$city->slug]?->minimum_advance ?? 0); ?>"
                                            <?php if(old('city_id', $cart->city_id ?: $selectedCity?->id) == $city->id): echo 'selected'; endif; ?>
                                        ><?php echo e($city->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Area / locality</label>
                                <select class="form-select" name="area_id" data-checkout-area>
                                    <option value="">Choose area</option>
                                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $__currentLoopData = $city->areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($area->id); ?>" data-city="<?php echo e($city->id); ?>" data-travel="<?php echo e($area->travel_fee); ?>" <?php if(old('area_id') == $area->id): echo 'selected'; endif; ?>><?php echo e($area->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full address</label>
                                <textarea class="form-control" name="full_address" rows="2" required><?php echo e(old('full_address', auth()->user()->address)); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Landmark</label>
                                <input class="form-control" name="landmark" value="<?php echo e(old('landmark')); ?>">
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
                                    <?php $__currentLoopData = ['Kids Birthday', 'Birthday Decoration', 'Anniversary', 'Welcome Baby', 'Naming Ceremony', 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php if(old('event_type') === $eventType): echo 'selected'; endif; ?>><?php echo e($eventType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Venue type</label>
                                <select class="form-select" name="venue_type" required>
                                    <?php $__currentLoopData = ['Home', 'Banquet', 'Society', 'School', 'Outdoor']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php if(old('venue_type') === $venue): echo 'selected'; endif; ?>><?php echo e($venue); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of kids</label>
                                <input class="form-control" type="number" name="number_of_kids" min="1" max="500" value="<?php echo e(old('number_of_kids', 15)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Age group</label>
                                <select class="form-select" name="age_group" required>
                                    <?php $__currentLoopData = ['1-3 years', '4-6 years', '7-10 years', '11-14 years', 'Mixed age group']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $age): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php if(old('age_group') === $age): echo 'selected'; endif; ?>><?php echo e($age); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Decoration theme</label>
                                <input class="form-control" name="decoration_theme" value="<?php echo e(old('decoration_theme')); ?>" placeholder="Optional: theme, colors or character">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Special instructions</label>
                                <textarea class="form-control" name="message" rows="3"><?php echo e(old('message')); ?></textarea>
                            </div>
                        </div>

                        <div class="booking-divider"></div>
                        <div class="booking-step-head">
                            <span>04</span>
                            <div><strong>Payment preference</strong><small>Pay advance or complete the payment</small></div>
                        </div>
                        <div class="payment-choice-grid">
                            <label class="payment-choice">
                                <input type="radio" name="payment_type" value="advance" <?php if(old('payment_type', 'advance') === 'advance'): echo 'checked'; endif; ?>>
                                <span><i class="fa-solid fa-calendar-check"></i><strong>Pay advance</strong><small>Reserve your date</small></span>
                            </label>
                            <label class="payment-choice">
                                <input type="radio" name="payment_type" value="full" <?php if(old('payment_type') === 'full'): echo 'checked'; endif; ?>>
                                <span><i class="fa-solid fa-shield-halved"></i><strong>Pay in full</strong><small>Finish payment now</small></span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Coupon code</label>
                            <input class="form-control" name="coupon_code" value="<?php echo e(old('coupon_code')); ?>" placeholder="PARTY10">
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <aside class="checkout-summary" data-checkout-summary>
                        <span class="mini-label">Booking summary</span>
                        <h2><?php echo e($cart->items->sum('quantity')); ?> selected item<?php echo e($cart->items->sum('quantity') === 1 ? '' : 's'); ?></h2>
                        <div class="checkout-items">
                            <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $prices = $item->service
                                        ? $item->service->cityPrices->mapWithKeys(fn ($price) => [$price->city_id => $price->effective_price])->all()
                                        : [];
                                    $addonsTotal = collect($item->selected_addons ?? [])->sum(
                                        fn ($addon) => (float) $addon['price'] * (int) ($addon['quantity'] ?? 1)
                                    );
                                ?>
                                <div
                                    class="checkout-item"
                                    data-quantity="<?php echo e($item->quantity); ?>"
                                    data-default-price="<?php echo e($item->unit_price); ?>"
                                    data-prices='<?php echo json_encode($prices, 15, 512) ?>'
                                    data-addons="<?php echo e($addonsTotal); ?>"
                                >
                                    <img src="<?php echo e($item->service?->image_url ?? $item->package?->image_url); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
                                    <span><strong><?php echo e($item->title); ?></strong><small>Qty <?php echo e($item->quantity); ?></small></span>
                                    <b>&#8377;<?php echo e(number_format($item->line_total)); ?></b>
                                </div>
                                <?php if($item->selected_addons): ?>
                                    <div class="checkout-addon-list">
                                        <?php $__currentLoopData = $item->selected_addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><img src="<?php echo e($addon['image'] ?? \App\Models\Addon::fallbackImageUrl()); ?>" alt="<?php echo e($addon['name']); ?>"><span><strong><?php echo e($addon['name']); ?></strong><small>Add-on</small></span><b>+&#8377;<?php echo e(number_format((float) $addon['price'])); ?></b></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="summary-lines">
                            <div><span>Items subtotal</span><strong data-checkout-subtotal>&#8377;<?php echo e(number_format($cart->subtotal)); ?></strong></div>
                            <div><span>City & travel fee</span><strong data-checkout-fee>&#8377;0</strong></div>
                            <div><span>Estimated tax</span><strong data-checkout-tax>&#8377;0</strong></div>
                            <div class="summary-total"><span>Total estimate</span><strong data-checkout-total>&#8377;<?php echo e(number_format($cart->subtotal)); ?></strong></div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/checkout/index.blade.php ENDPATH**/ ?>
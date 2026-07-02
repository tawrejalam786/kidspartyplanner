<?php $__env->startSection('content'); ?>
<section class="detail-section">
    <div class="container">
        <nav class="detail-breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Home</a><i class="fa-solid fa-chevron-right"></i>
            <a href="<?php echo e(route('categories.show', $service->category)); ?>"><?php echo e($service->category->name); ?></a>
            <?php if($service->subcategory): ?><i class="fa-solid fa-chevron-right"></i><a href="<?php echo e(route('subcategories.show', $service->subcategory)); ?>"><?php echo e($service->subcategory->name); ?></a><?php endif; ?>
        </nav>
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="swiper detail-swiper"><div class="swiper-wrapper"><?php $__currentLoopData = $service->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="swiper-slide"><img src="<?php echo e($image->url); ?>" alt="<?php echo e($image->alt_text ?: $service->title); ?>" loading="<?php echo e($loop->first ? 'eager' : 'lazy'); ?>"></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><!-- Left Right Arrows -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div><div class="swiper-pagination"></div></div>
                <div class="detail-content">
                    <div class="d-flex align-items-center gap-2 flex-wrap"><span class="badge soft-badge"><?php echo e($service->category->name); ?></span><?php if($service->trending): ?><span class="badge text-bg-warning">Trending</span><?php endif; ?></div>
                    <h1><?php echo e($service->title); ?></h1>
                    <div class="detail-meta"><span><i class="fa-solid fa-star"></i> <?php echo e($service->rating); ?> (<?php echo e($service->total_reviews); ?> reviews)</span><span><i class="fa-regular fa-clock"></i> <?php echo e($service->duration); ?></span><span><i class="fa-solid fa-location-dot"></i> <?php echo e($selectedCity?->name); ?></span></div>
                    <div class="detail-price"><small>Starting price in <?php echo e($selectedCity?->name); ?></small><strong>&#8377;<?php echo e(number_format($displayPrice)); ?></strong><?php if($displayPrice < (float) $service->price): ?><del>&#8377;<?php echo e(number_format((float) $service->price)); ?></del><?php endif; ?></div>
                    <p class="detail-lead"><?php echo e($service->description); ?></p>
                    <div class="detail-fact-grid"><div><i class="fa-solid fa-child-reaching"></i><span>Age group<strong><?php echo e($service->age_group ?: 'All kids'); ?></strong></span></div><div><i class="fa-solid fa-people-group"></i><span>Kids capacity<strong>Up to <?php echo e($service->kids_capacity ?: 25); ?></strong></span></div><div><i class="fa-solid fa-city"></i><span>Available in<strong><?php echo e($service->availableCities->where('pivot.is_available', true)->pluck('name')->join(', ') ?: 'Delhi NCR'); ?></strong></span></div></div>
                    <div class="detail-info-grid">
                        <section><h2><i class="fa-solid fa-check"></i> What is included</h2><ul class="check-list"><?php $__currentLoopData = $service->inclusions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($item); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></section>
                        <section><h2><i class="fa-solid fa-xmark"></i> Not included</h2><ul class="cross-list"><?php $__currentLoopData = $service->exclusions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($item); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></section>
                        <section><h2><i class="fa-solid fa-clipboard-check"></i> Customer requirements</h2><ul class="check-list"><?php $__currentLoopData = $service->requirements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($item); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></section>
                        <section><h2><i class="fa-solid fa-calendar-xmark"></i> Cancellation</h2><p><?php echo e($service->cancellation_policy); ?></p></section>
                    </div>
                    <?php if($service->terms): ?><div class="detail-terms"><h2>Service terms</h2><p><?php echo e($service->terms); ?></p></div><?php endif; ?>
                    <?php if($serviceFaqs->isNotEmpty()): ?>
                        <h2 class="mt-5">Service questions</h2>
                        <div class="accordion" id="serviceFaq">
                            <?php $__currentLoopData = $serviceFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#serviceFaq<?php echo e($loop->index); ?>"><?php echo e($faq['question']); ?></button></h3>
                                    <div id="serviceFaq<?php echo e($loop->index); ?>" class="accordion-collapse collapse" data-bs-parent="#serviceFaq"><div class="accordion-body"><?php echo e($faq['answer']); ?></div></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <aside class="service-booking-sidebar">
                    <div class="booking-panel">
                        <div class="service-booking-head"><div><span class="mini-label">Reserve this experience</span><h2>&#8377;<?php echo e(number_format($displayPrice)); ?></h2><small><?php echo e($selectedCity?->name); ?> starting price</small></div><a class="whatsapp-btn" href="https://wa.me/<?php echo e(\App\Models\Setting::getValue('whatsapp_number')); ?>?text=<?php echo e(urlencode('Hi, I want to enquire about '.$service->title)); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a></div>
                        <form class="detail-cart-form" method="post" action="<?php echo e(route('cart.add')); ?>">
                            <?php echo csrf_field(); ?><input type="hidden" name="service_id" value="<?php echo e($service->id); ?>"><input type="hidden" name="city_id" value="<?php echo e($selectedCity?->id); ?>">
                            <?php if($service->addons->isNotEmpty()): ?>
                                <label class="form-label">Choose add-ons</label>
                                <div class="detail-addon-list">
                                    <?php $__currentLoopData = $service->addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="detail-addon-card">
                                            <input type="checkbox" name="addon_ids[]" value="<?php echo e($addon->id); ?>">
                                            <img src="<?php echo e($addon->image_url); ?>" alt="<?php echo e($addon->name); ?>" loading="lazy">
                                            <span><strong><?php echo e($addon->name); ?></strong><small>+&#8377;<?php echo e(number_format((float) ($addon->pivot->price_override ?: $addon->price))); ?></small></span>
                                            <i class="fa-solid fa-check"></i>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-outline-party w-100 mt-3" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to party cart</button>
                        </form>
                        <?php if(auth()->guard()->check()): ?><form method="post" action="<?php echo e(route('wishlist.store', $service)); ?>" class="mt-2"><?php echo csrf_field(); ?><button class="btn btn-light border w-100"><i class="fa-regular fa-heart"></i> Save to wishlist</button></form><?php endif; ?>
                        <div class="booking-divider"></div>
                        <?php echo $__env->make('partials.booking-form', ['service' => $service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="sidebar-trust"><span><i class="fa-solid fa-shield-halved"></i> Secure Razorpay payment</span><span><i class="fa-solid fa-headset"></i> Human booking support</span><span><i class="fa-solid fa-receipt"></i> Transparent city pricing</span></div>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php if($service->reviews->isNotEmpty()): ?><section class="section section-soft"><div class="container"><div class="section-heading"><span>Verified reviews</span><h2>Parents on this experience</h2></div><div class="row g-4"><?php $__currentLoopData = $service->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-4"><article class="testimonial-card"><div class="stars"><?php for($i=0;$i<$review->rating;$i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?></div><p><?php echo e($review->comment); ?></p><strong><?php echo e($review->customer_name); ?></strong></article></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></section><?php endif; ?>
<?php if($relatedServices->isNotEmpty()): ?><section class="section"><div class="container"><div class="section-heading with-action"><div><span>Related services</span><h2>More ideas for the party</h2></div><a class="arrow-link" href="<?php echo e(route('categories.show', $service->category)); ?>">View category <i class="fa-solid fa-arrow-right"></i></a></div><div class="row g-3 g-md-4"><?php $__currentLoopData = $relatedServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-6 col-xl-3"><?php echo $__env->make('partials.service-card', ['service' => $related], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></section><?php endif; ?>
<div class="mobile-booking-bar"><div><small>From</small><strong>&#8377;<?php echo e(number_format($displayPrice)); ?></strong></div><a class="btn btn-party" href="<?php echo e(route('booking.create', ['service' => $service->slug])); ?>">Book now</a></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\services\show.blade.php ENDPATH**/ ?>
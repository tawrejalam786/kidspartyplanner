<?php $__env->startSection('content'); ?>
<section class="hero-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($hasBannerCopy = filled($banner->title) || filled($banner->subtitle) || filled($banner->button_text)); ?>
                <div class="swiper-slide hero-slide <?php echo e($hasBannerCopy ? 'has-copy' : 'image-only'); ?>">
                    <img class="hero-banner-image" src="<?php echo e($banner->image_url); ?>" alt="<?php echo e($banner->title ?: 'Kids Party Planner banner'); ?>">
                    <?php if($hasBannerCopy): ?>
                        <div class="container">
                            <div class="hero-copy d-none">
                                <span class="hero-kicker"><i class="fa-solid fa-location-dot"></i> <?php echo e($selectedCity?->name ?? 'Delhi NCR'); ?> party booking</span>
                                <?php if(filled($banner->title)): ?>
                                    <?php if($loop->first): ?>
                                        <h1>Kids Party Planner</h1>
                                        <strong class="hero-offer"><?php echo e($banner->title); ?></strong>
                                    <?php else: ?>
                                        <h2><?php echo e($banner->title); ?></h2>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(filled($banner->subtitle)): ?>
                                    <p><?php echo e($banner->subtitle); ?></p>
                                <?php endif; ?>
                                <?php if(filled($banner->button_text)): ?>
                                    <div class="hero-actions">
                                        <a class="btn btn-party btn-lg" href="<?php echo e(url($banner->button_url ?: '/services')); ?>"><?php echo e($banner->button_text); ?> <i class="fa-solid fa-arrow-right"></i></a>
                                        <a class="text-action" href="<?php echo e(route('booking.track')); ?>">Track a booking <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="search-band">
    <div class="container">
        <form action="<?php echo e(route('services.index')); ?>" class="search-box">
            <input type="hidden" name="city" value="<?php echo e($selectedCity?->slug); ?>">
            <div class="search-field search-main"><i class="fa-solid fa-magnifying-glass"></i><div><label>What should we bring to the party?</label><input type="search" name="search" placeholder="Try magic, slime or face painting"></div></div>
            <div class="search-field"><i class="fa-solid fa-indian-rupee-sign"></i><div><label>Your budget</label><select name="max_price"><option value="">Any budget</option><option value="2000">Under &#8377;2,000</option><option value="3000">Under &#8377;3,000</option><option value="5000">Under &#8377;5,000</option></select></div></div>
            <button class="btn btn-party" type="submit" aria-label="Search services"><i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <div class="city-quick-links"><span>Popular:</span><a href="<?php echo e(route('services.index')); ?>?search=Magic">Magic show</a><a href="<?php echo e(route('services.index')); ?>?search=Games">Party games</a><a href="<?php echo e(route('services.index')); ?>?search=Face+Painting">Face painting</a><a href="<?php echo e(route('services.index')); ?>?search=Slime">Slime workshop</a></div>
        <div class="home-city-selector"><span>Choose your city</span><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><form method="post" action="<?php echo e(route('city.select')); ?>"><?php echo csrf_field(); ?><input type="hidden" name="city_id" value="<?php echo e($city->id); ?>"><button class="<?php echo e($selectedCity?->id === $city->id ? 'active' : ''); ?>"><i class="fa-solid fa-location-dot"></i> <?php echo e($city->name); ?></button></form><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    </div>
</section>

<section class="section category-section">
    <div class="container">
        <div class="section-heading with-action"><div><span>Find their kind of fun</span><h2>What are we celebrating with?</h2></div><a href="<?php echo e(route('services.index')); ?>" class="arrow-link">See everything <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="category-grid">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="category-tile" href="<?php echo e(route('categories.show', $category)); ?>">
                    <img src="<?php echo e($category->image_url); ?>" alt="<?php echo e($category->name); ?>" loading="lazy">
                    <div><span><?php echo e(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span><h3><?php echo e($category->name); ?></h3><p><?php echo e($category->services_count); ?> ways to celebrate</p></div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-heading with-action"><div><span>Parent favourites</span><h2>Booked again and again</h2></div><a href="<?php echo e(route('services.index')); ?>" class="arrow-link">Browse all services <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="row g-4">
            <?php $__currentLoopData = $featuredServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-xl-3"><?php echo $__env->make('partials.service-card', ['service' => $service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php $__currentLoopData = [
    'kids-activities-games' => ['Kids activities & games', 'Bring the energy, laughter and hands-on fun.'],
    'birthday-decoration' => ['Birthday decoration', 'From simple balloons to complete character themes.'],
    'anniversary-decoration' => ['Anniversary decoration', 'Warm, romantic setups for meaningful milestones.'],
    'new-born-baby-decoration' => ['New born baby decoration', 'Welcome-home and naming ceremony celebrations.'],
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => [$title, $copy]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php ($shelf = $categoryServices->get($slug)); ?>
    <?php if($shelf && $shelf->services->isNotEmpty()): ?>
        <section class="section category-shelf <?php echo e($loop->even ? 'section-soft' : ''); ?>"><div class="container"><div class="section-heading with-action"><div><span>Explore by celebration</span><h2><?php echo e($title); ?></h2><p class="section-lead"><?php echo e($copy); ?></p></div><a class="arrow-link" href="<?php echo e(route('categories.show',$shelf)); ?>">View category <i class="fa-solid fa-arrow-right"></i></a></div><div class="row g-3 g-md-4"><?php $__currentLoopData = $shelf->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-6 col-xl-3"><?php echo $__env->make('partials.service-card',['service'=>$service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></section>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<section class="section package-showcase">
    <div class="container">
        <div class="row g-5 align-items-end mb-4"><div class="col-lg-7"><div class="section-heading text-start mb-0"><span>Ready-made party plans</span><h2>Good decisions, already bundled.</h2></div></div><div class="col-lg-5"><p class="section-lead">Pick a package when you want the entertainment, activities and coordination sorted together.</p></div></div>
        <div class="row g-4">
            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-xl-3"><?php echo $__env->make('partials.package-card', ['package' => $package], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="section process-section">
    <div class="container">
        <div class="section-heading"><span>Four calm steps</span><h2>From idea to party day</h2></div>
        <div class="process-grid">
            <?php $__currentLoopData = [
                ['01', 'fa-magnifying-glass', 'Discover', 'Compare activities, inclusions and local prices.'],
                ['02', 'fa-location-dot', 'Add details', 'Tell us the city, venue, date and kids count.'],
                ['03', 'fa-credit-card', 'Reserve', 'Pay the city-specific advance or the full amount.'],
                ['04', 'fa-face-smile', 'Celebrate', 'Track confirmation while our team gets party-ready.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="process-card"><span><?php echo e($step[0]); ?></span><i class="fa-solid <?php echo e($step[1]); ?>"></i><h3><?php echo e($step[2]); ?></h3><p><?php echo e($step[3]); ?></p></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="trust-band">
    <div class="container">
        <div class="trust-copy"><span class="mini-label">Made for real birthday logistics</span><h2>Happy kids. Unhurried parents.</h2><p>Clear inclusions, verified artists and one dashboard from payment to completion.</p><a class="btn btn-dark" href="<?php echo e(route('about')); ?>">Why parents choose us</a></div>
        <div class="trust-photo"><img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=86" alt="Children enjoying a birthday celebration" loading="lazy"><div class="trust-stat"><strong>4.8/5</strong><span>Average parent rating</span></div></div>
        <div class="trust-points"><div><i class="fa-solid fa-user-check"></i><strong>Trained artists</strong><span>Child-friendly entertainers and coordinators.</span></div><div><i class="fa-solid fa-receipt"></i><strong>Clear pricing</strong><span>See city fees and advance before checkout.</span></div><div><i class="fa-solid fa-headset"></i><strong>Human support</strong><span>Real coordinators before and during the event.</span></div></div>
    </div>
</section>

<section class="section gallery-section">
    <div class="container">
        <div class="section-heading with-action"><div><span>Party scenes</span><h2>A little proof of fun</h2></div><a href="<?php echo e(route('gallery')); ?>" class="arrow-link">Open gallery <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="gallery-grid home-gallery"><?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><figure><img src="<?php echo e($item->image_url); ?>" alt="<?php echo e($item->title); ?>" loading="lazy"><figcaption><?php echo e($item->title); ?><span><?php echo e($item->type); ?></span></figcaption></figure><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    </div>
</section>

<section class="section testimonial-section">
    <div class="container">
        <div class="section-heading"><span>Notes from parents</span><h2>The best feedback is “again!”</h2></div>
        <div class="swiper testimonial-swiper"><div class="swiper-wrapper"><?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="swiper-slide"><article class="testimonial-card"><div class="quote-mark">&ldquo;</div><div class="stars"><?php for($i = 0; $i < $review->rating; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?></div><p><?php echo e($review->comment); ?></p><div class="review-author"><span><?php echo e(strtoupper(substr($review->customer_name, 0, 1))); ?></span><div><strong><?php echo e($review->customer_name); ?></strong><small>Verified parent</small></div></div></article></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
    </div>
</section>

<section class="social-cta"><div class="container"><div><span class="mini-label"><i class="fa-brands fa-instagram"></i> @kidspartyplanner1</span><h2>Fresh setups, real parties, new ideas.</h2><p>Follow our latest Delhi NCR celebrations and theme inspiration.</p></div><a class="btn btn-light" href="<?php echo e(\App\Models\Setting::getValue('instagram_url','https://www.instagram.com/kidspartyplanner1/')); ?>" target="_blank" rel="noopener">Follow on Instagram <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div></section>

<section class="section faq-section">
    <div class="container"><div class="row g-5"><div class="col-lg-4"><div class="section-heading text-start"><span>Questions, answered</span><h2>Before you book</h2></div><p class="section-lead">Need something unusual? Our WhatsApp team can help shape a custom plan.</p></div><div class="col-lg-8"><div class="accordion" id="homeFaq"><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button <?php if(!$loop->first): ?> collapsed <?php endif; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($loop->index); ?>"><?php echo e($faq['question']); ?></button></h3><div id="faq<?php echo e($loop->index); ?>" class="accordion-collapse collapse <?php if($loop->first): ?> show <?php endif; ?>" data-bs-parent="#homeFaq"><div class="accordion-body"><?php echo e($faq['answer']); ?></div></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></div></div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/home.blade.php ENDPATH**/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($metaTitle ?? \App\Models\Setting::getValue('meta_title', config('app.name'))); ?></title>
    <meta name="description" content="<?php echo e($metaDescription ?? \App\Models\Setting::getValue('meta_description', 'Kids birthday party booking in Delhi NCR.')); ?>">
    <?php if($metaKeywords ?? null): ?><meta name="keywords" content="<?php echo e($metaKeywords); ?>"><?php endif; ?>
    <meta property="og:title" content="<?php echo e($metaTitle ?? \App\Models\Setting::getValue('meta_title', config('app.name'))); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription ?? \App\Models\Setting::getValue('meta_description', '')); ?>">
    <?php if($ogImage ?? null): ?><meta property="og:image" content="<?php echo e($ogImage); ?>"><?php endif; ?>
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <header class="mobile-app-header">
        <div class="mobile-service-strip">
            <span><i class="fa-solid fa-bolt"></i> Earliest <strong>tomorrow</strong></span>
            <span><i class="fa-regular fa-calendar"></i> Book up to <strong>90 days</strong></span>
        </div>
        <div class="mobile-location-row">
            <a class="mobile-brand" href="<?php echo e(route('home')); ?>" aria-label="Kids Party Planner home"><span class="brand-logo-frame mobile-logo-frame"><img src="<?php echo e(asset('assets/images/kidspartyplanner-logo.png')); ?>" alt="Kids Party Planner"></span></a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#cityModal"><i class="fa-solid fa-location-dot"></i><span><?php echo e($selectedCity?->name ?? 'Select city'); ?></span><i class="fa-solid fa-chevron-down"></i></button>
        </div>
        <div class="mobile-search-row">
            <form action="<?php echo e(route('services.index')); ?>">
                <input type="hidden" name="city" value="<?php echo e($selectedCity?->slug); ?>">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" name="search" placeholder="Search activities and decoration">
                <button aria-label="Search"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
            <?php if(auth()->guard()->check()): ?>
                <a class="mobile-account-button" href="<?php echo e(route(auth()->user()->dashboardRouteName())); ?>" aria-label="Dashboard"><?php if(auth()->user()->avatar_url): ?><img src="<?php echo e(auth()->user()->avatar_url); ?>" alt=""><?php else: ?><i class="fa-regular fa-user"></i><?php endif; ?></a>
            <?php else: ?>
                <a class="mobile-login-button" href="<?php echo e(route('login')); ?>"><i class="fa-regular fa-user"></i> Login</a>
            <?php endif; ?>
        </div>
        <nav class="mobile-category-nav" aria-label="Party categories">
            <a class="<?php echo e(request()->routeIs('categories.index') ? 'active' : ''); ?>" href="<?php echo e(route('categories.index')); ?>"><i class="fa-solid fa-border-all"></i> All</a>
            <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="<?php echo e(request()->routeIs('categories.show') && request()->route('category') instanceof \App\Models\Category && request()->route('category')->is($category) ? 'active' : ''); ?>" href="<?php echo e(route('categories.show', $category)); ?>"><?php echo e($category->name); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </header>

    <header class="site-header desktop-site-header sticky-top">
        <div class="utility-bar">
            <div class="container">
                <span><i class="fa-solid fa-location-dot"></i> Serving Delhi, Noida & Gurgaon</span>
                <div><a href="mailto:<?php echo e(\App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in')); ?>"><i class="fa-solid fa-envelope"></i> <?php echo e(\App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in')); ?></a><a href="<?php echo e(\App\Models\Setting::getValue('instagram_url', 'https://www.instagram.com/kidspartyplanner1/')); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i> Instagram</a><a href="tel:<?php echo e(preg_replace('/\s+/', '', \App\Models\Setting::getValue('site_phone', '+91 9910434330'))); ?>"><i class="fa-solid fa-phone"></i> <?php echo e(\App\Models\Setting::getValue('site_phone', '+91 9910434330')); ?></a></div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg p-0">
            <div class="container">
                <a class="navbar-brand" href="<?php echo e(route('home')); ?>" aria-label="Kids Party Planner home">
                    <span class="brand-logo-frame desktop-logo-frame"><img src="<?php echo e(asset('assets/images/kidspartyplanner-logo.png')); ?>" alt="Kids Party Planner"></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto align-items-lg-center">
                        <li class="nav-item dropdown categories-dropdown">
                            <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('categories.*','subcategories.*') ? 'active' : ''); ?>" href="<?php echo e(route('categories.index')); ?>" data-bs-toggle="dropdown" data-bs-auto-close="outside">Discover Celebrations</a>
                            <div class="dropdown-menu category-mega-menu">
                                <div class="category-menu-head"><span>Explore celebrations</span><a href="<?php echo e(route('categories.index')); ?>">View all <i class="fa-solid fa-arrow-right"></i></a></div>
                                <div class="category-menu-grid">
                                    <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="category-menu-item">
                                            <a class="category-menu-title" href="<?php echo e(route('categories.show', $category)); ?>"><img src="<?php echo e($category->image_url); ?>" alt=""><span><strong><?php echo e($category->name); ?></strong><small><?php echo e($category->description); ?></small></span></a>
                                            <?php $__currentLoopData = $category->subcategories->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="<?php echo e(route('subcategories.show', $subcategory)); ?>"><?php echo e($subcategory->name); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('services.*', 'categories.*') ? 'active' : ''); ?>" href="<?php echo e(route('services.index')); ?>">Services</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('packages.*') ? 'active' : ''); ?>" href="<?php echo e(route('packages.index')); ?>">Packages</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('gallery') ? 'active' : ''); ?>" href="<?php echo e(route('gallery')); ?>">Gallery</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('reviews') ? 'active' : ''); ?>" href="<?php echo e(route('reviews')); ?>">Reviews</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('about') ? 'active' : ''); ?>" href="<?php echo e(route('about')); ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo e(request()->routeIs('booking.track') ? 'active' : ''); ?>" href="<?php echo e(route('booking.track')); ?>">Track</a></li>
                    </ul>
                    <div class="nav-actions">
                        <button class="city-trigger" type="button" data-bs-toggle="modal" data-bs-target="#cityModal"><i class="fa-solid fa-location-dot"></i><span><?php echo e($selectedCity?->name ?? 'Select city'); ?></span><i class="fa-solid fa-chevron-down"></i></button>
                        <a class="icon-link cart-link" href="<?php echo e(route('cart.index')); ?>" title="Party cart" aria-label="Party cart"><i class="fa-solid fa-bag-shopping"></i><?php if($cartCount): ?><span><?php echo e($cartCount); ?></span><?php endif; ?></a>
                        <?php if(auth()->guard()->check()): ?>
                            <a class="icon-link" href="<?php echo e(route(auth()->user()->dashboardRouteName())); ?>" title="Dashboard" aria-label="Dashboard"><i class="fa-regular fa-user"></i></a>
                            <form action="<?php echo e(route('logout')); ?>" method="post"><?php echo csrf_field(); ?><button class="icon-link" title="Logout" aria-label="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
                        <?php else: ?>
                            <a class="login-link" href="<?php echo e(route('login')); ?>"><i class="fa-regular fa-user"></i> Login</a>
                        <?php endif; ?>
                        <a class="btn btn-party btn-sm" href="<?php echo e(route('booking.create')); ?>">Book a party <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <?php if(session('success') || $errors->any()): ?>
        <div class="container flash-wrap">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo e($errors->first()); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <main><?php echo $__env->yieldContent('content'); ?></main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-intro">
                <div><span class="mini-label">Party help, minus the panic</span><h2>Make the next birthday the easy one.</h2></div>
                <a class="btn btn-light" href="<?php echo e(route('booking.create')); ?>">Start booking <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="row g-4 footer-links">
                <div class="col-lg-4">
                    <a class="footer-brand" href="<?php echo e(route('home')); ?>"><span class="brand-logo-frame footer-logo-frame"><img src="<?php echo e(asset('assets/images/kidspartyplanner-logo.png')); ?>" alt="Kids Party Planner"></span></a>
                    <p>Entertainers, activity artists and ready party packages for joyful birthdays across Delhi NCR.</p>
                    <p class="footer-address"><i class="fa-solid fa-location-dot"></i> <?php echo e(\App\Models\Setting::getValue('site_address', 'TC-37, Pandav Nagar, Shadipur, New Delhi - 110008')); ?></p>
                </div>
                <div class="col-6 col-lg-2"><h6>Discover</h6><a href="<?php echo e(route('categories.index')); ?>">Categories</a><a href="<?php echo e(route('services.index')); ?>">Services</a><a href="<?php echo e(route('packages.index')); ?>">Packages</a><a href="<?php echo e(route('gallery')); ?>">Gallery</a></div>
                <div class="col-6 col-lg-2"><h6>Company</h6><a href="<?php echo e(route('about')); ?>">About</a><a href="<?php echo e(route('reviews')); ?>">Reviews</a><a href="<?php echo e(route('faq')); ?>">FAQ</a><a href="<?php echo e(route('contact')); ?>">Contact</a></div>
                <div class="col-6 col-lg-2"><h6>Legal</h6><a href="<?php echo e(route('terms')); ?>">Terms</a><a href="<?php echo e(route('privacy')); ?>">Privacy</a><a href="<?php echo e(route('refund')); ?>">Refunds</a></div>
                <div class="col-6 col-lg-2"><h6>Talk to us</h6><a href="tel:<?php echo e(preg_replace('/\s+/', '', \App\Models\Setting::getValue('site_phone', '+91 9910434330'))); ?>">Call us</a><a href="mailto:<?php echo e(\App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in')); ?>">Email</a><a href="<?php echo e(\App\Models\Setting::getValue('instagram_url', '#')); ?>" target="_blank">Instagram</a><a href="<?php echo e(route('booking.track')); ?>">Track booking</a></div>
            </div>
            <div class="footer-bottom"><span>&copy; <?php echo e(date('Y')); ?> Kids Party Planner</span><span>Made for happier birthdays in Delhi NCR</span></div>
        </div>
    </footer>

    <?php if($recentBookingActivity->isNotEmpty()): ?>
        <div class="booking-activity-feed" aria-live="polite">
            <?php $__currentLoopData = $recentBookingActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="booking-activity-item" data-booking-activity>
                    <span><i class="fa-solid fa-check"></i></span>
                    <div><strong><?php echo e(\Illuminate\Support\Str::before($activity->customer_name, ' ')); ?> booked <?php echo e($activity->item_title); ?></strong><small><?php echo e($activity->city?->name ?? $activity->cityPaymentSetting?->city ?? 'Delhi NCR'); ?> &middot; <?php echo e($activity->updated_at->diffForHumans()); ?></small></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <a class="whatsapp-float" href="https://wa.me/<?php echo e(\App\Models\Setting::getValue('whatsapp_number', config('services.whatsapp.number'))); ?>?text=<?php echo e(urlencode('Hi Kids Party Planner, I want to enquire about a birthday party booking.')); ?>" target="_blank" rel="noopener" aria-label="WhatsApp enquiry"><i class="fa-brands fa-whatsapp"></i><span>Enquire</span></a>

    <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
        <a class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a class="<?php echo e(request()->routeIs('categories.*','subcategories.*') ? 'active' : ''); ?>" href="<?php echo e(route('categories.index')); ?>"><i class="fa-solid fa-shapes"></i><span>Categories</span></a>
        <a class="mobile-book-action" href="<?php echo e(route('booking.create')); ?>"><i class="fa-solid fa-calendar-plus"></i><span>Book</span></a>
        <a class="<?php echo e(request()->routeIs('cart.*','checkout.*') ? 'active' : ''); ?>" href="<?php echo e(route('cart.index')); ?>"><i class="fa-solid fa-cart-shopping"></i><?php if($cartCount): ?><b><?php echo e($cartCount); ?></b><?php endif; ?><span>Cart</span></a>
        <a class="<?php echo e(request()->routeIs('dashboard*','vendor.*','login') ? 'active' : ''); ?>" href="<?php echo e(auth()->check() ? route(auth()->user()->dashboardRouteName()) : route('login')); ?>"><i class="fa-regular fa-user"></i><span><?php echo e(auth()->check() ? 'Account' : 'Login'); ?></span></a>
    </nav>

    <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content city-modal"><div class="modal-header"><div><span class="mini-label">Service location</span><h2 id="cityModalLabel">Where is the party?</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="city-options"><?php $__currentLoopData = $activeCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><form method="post" action="<?php echo e(route('city.select')); ?>"><?php echo csrf_field(); ?><input type="hidden" name="city_id" value="<?php echo e($city->id); ?>"><button class="city-option <?php echo e($selectedCity?->id === $city->id ? 'active' : ''); ?>"><i class="fa-solid fa-location-dot"></i><span><strong><?php echo e($city->name); ?></strong><small><?php echo e($city->state); ?></small></span><?php if($selectedCity?->id === $city->id): ?><i class="fa-solid fa-check"></i><?php endif; ?></button></form><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php if($futureCities->isNotEmpty()): ?><span class="mini-label mt-4">Coming soon</span><div class="future-cities"><?php $__currentLoopData = $futureCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($city->name); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?></div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/layouts/app.blade.php ENDPATH**/ ?>
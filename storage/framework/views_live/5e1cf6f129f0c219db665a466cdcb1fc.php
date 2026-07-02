<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Admin Panel'); ?> | Kids Party Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
</head>
<body class="admin-body">
    <?php
        $resources = [
            'bookings' => ['Bookings', 'fa-ticket'],
            'payments' => ['Payments', 'fa-credit-card'],
            'refunds' => ['Refunds', 'fa-rotate-left'],
            'cities' => ['Cities', 'fa-city'],
            'areas' => ['Areas', 'fa-map-location-dot'],
            'city-payments' => ['City Payments', 'fa-building-columns'],
            'services' => ['Services', 'fa-wand-magic-sparkles'],
            'service-images' => ['Service Images', 'fa-images'],
            'service-prices' => ['Service Pricing', 'fa-indian-rupee-sign'],
            'addons' => ['Add-ons', 'fa-puzzle-piece'],
            'packages' => ['Packages', 'fa-box-open'],
            'categories' => ['Categories', 'fa-shapes'],
            'subcategories' => ['Subcategories', 'fa-layer-group'],
            'customers' => ['Customers', 'fa-users'],
            'admins' => ['Admin Users', 'fa-user-shield'],
            'vendors' => ['Vendors', 'fa-handshake'],
            'booking-assignments' => ['Assignments', 'fa-clipboard-check'],
            'vendor-earnings' => ['Vendor Earnings', 'fa-wallet'],
            'vendor-withdrawals' => ['Withdrawals', 'fa-money-bill-transfer'],
            'enquiries' => ['Enquiries', 'fa-inbox'],
            'reviews' => ['Reviews', 'fa-star'],
            'galleries' => ['Gallery', 'fa-images'],
            'banners' => ['Banners', 'fa-panorama'],
            'faqs' => ['FAQs', 'fa-circle-question'],
            'coupons' => ['Coupons', 'fa-tags'],
            'blogs' => ['Blogs', 'fa-pen-nib'],
            'pages' => ['CMS Pages', 'fa-file-lines'],
            'settings' => ['Settings', 'fa-gear'],
        ];
    ?>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-head">
                <a class="admin-brand" href="<?php echo e(route('admin.dashboard')); ?>"><span class="brand-logo-frame admin-logo-frame"><img src="<?php echo e(asset('assets/images/kids-party-planner-logo.jpg')); ?>" alt="Kids Party Planner"></span><span>Admin</span></a>
                <button class="admin-menu-toggle" type="button" aria-label="Toggle admin navigation"><i class="fa-solid fa-bars"></i></button>
            </div>
            <nav>
                <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => [$label, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="<?php echo e(request()->is('admin/'.$slug.'*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.resources.index', $slug)); ?>">
                        <i class="fa-solid <?php echo e($icon); ?>"></i> <?php echo e($label); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
            <form action="<?php echo e(route('logout')); ?>" method="post"><?php echo csrf_field(); ?><button class="btn btn-outline-light w-100 mt-3">Logout</button></form>
        </aside>
        <div class="admin-main">
            <div class="admin-topbar">
                <div>
                    <span>Admin Panel</span>
                    <h1><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                </div>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-party btn-sm">View Site</a>
            </div>
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
    <script>document.querySelector('.admin-menu-toggle')?.addEventListener('click', function () { document.querySelector('.admin-sidebar')?.classList.toggle('sidebar-open'); });</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/layouts/admin.blade.php ENDPATH**/ ?>
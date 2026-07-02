<?php $__env->startSection('content'); ?>
<section class="detail-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <img class="detail-main-image" src="<?php echo e($package->image_url); ?>" alt="<?php echo e($package->title); ?>" loading="lazy">
                <div class="detail-content">
                    <span class="badge soft-badge">Party Package</span>
                    <h1><?php echo e($package->title); ?></h1>
                    <div class="detail-meta"><span><i class="fa-solid fa-clock"></i> <?php echo e($package->duration); ?></span><span><i class="fa-solid fa-location-dot"></i> Delhi NCR</span></div>
                    <div class="detail-price">Rs. <?php echo e(number_format($package->effective_price)); ?> <?php if($package->discount_price): ?><del>Rs. <?php echo e(number_format((float) $package->price)); ?></del><?php endif; ?></div>
                    <p><?php echo e($package->description); ?></p>
                    <h3>Services Included</h3>
                    <ul class="check-list"><?php $__currentLoopData = $package->services ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($item); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    <h3>Package Inclusions</h3>
                    <ul class="check-list"><?php $__currentLoopData = $package->inclusions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($item); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="booking-panel">
                    <span class="eyebrow">Book package</span>
                    <h2><?php echo e($package->title); ?></h2>
                    <?php echo $__env->make('partials.booking-form', ['package' => $package], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if($relatedPackages->isNotEmpty()): ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-heading"><span>More packages</span><h2>Other ready plans</h2></div>
        <div class="row g-4"><?php $__currentLoopData = $relatedPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-4"><?php echo $__env->make('partials.package-card', ['package' => $related], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\packages\show.blade.php ENDPATH**/ ?>
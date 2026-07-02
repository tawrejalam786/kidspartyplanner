<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Packages</span>
        <h1>Kids Birthday Party Packages</h1>
        <p>Curated combinations of entertainment, games, crafts, and coordination for easier planning.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-xl-4"><?php echo $__env->make('partials.package-card', ['package' => $package], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-4"><?php echo e($packages->links()); ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/packages/index.blade.php ENDPATH**/ ?>
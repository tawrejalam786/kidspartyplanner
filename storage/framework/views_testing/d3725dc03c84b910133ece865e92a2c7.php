<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Gallery</span>
        <h1>Party snapshots</h1>
        <p>Activity corners, entertainment moments, games, and celebration setups.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="gallery-grid large">
            <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <figure>
                    <img src="<?php echo e($item->image_url); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
                    <figcaption><?php echo e($item->title); ?> <span><?php echo e($item->type); ?></span></figcaption>
                </figure>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-4"><?php echo e($gallery->links()); ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/pages/gallery.blade.php ENDPATH**/ ?>
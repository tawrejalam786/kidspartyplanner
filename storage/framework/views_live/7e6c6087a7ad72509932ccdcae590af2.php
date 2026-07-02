<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Blog</span>
        <h1><?php echo e($blog->title); ?></h1>
        <p><?php echo e($blog->excerpt); ?></p>
    </div>
</section>
<section class="section">
    <div class="container narrow-content">
        <img class="detail-main-image mb-4" src="<?php echo e($blog->image_url); ?>" alt="<?php echo e($blog->title); ?>" loading="lazy">
        <div class="content-body"><?php echo $blog->content; ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\pages\blog-show.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Blog</span>
        <h1>Party planning ideas</h1>
        <p>Guides for choosing kids birthday entertainment, activities, and schedules.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-xl-4">
                    <a class="blog-card" href="<?php echo e(route('blog.show', $blog)); ?>">
                        <img src="<?php echo e($blog->image_url); ?>" alt="<?php echo e($blog->title); ?>" loading="lazy">
                        <div><span><?php echo e(optional($blog->published_at)->format('d M Y')); ?></span><h2><?php echo e($blog->title); ?></h2><p><?php echo e($blog->excerpt); ?></p></div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-4"><?php echo e($blogs->links()); ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\pages\blog-index.blade.php ENDPATH**/ ?>
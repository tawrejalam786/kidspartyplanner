<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Policy</span>
        <h1><?php echo e($page->title); ?></h1>
    </div>
</section>
<section class="section">
    <div class="container narrow-content">
        <div class="content-body"><?php echo $page->content; ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/pages/policy.blade.php ENDPATH**/ ?>
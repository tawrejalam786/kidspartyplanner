<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php $__currentLoopData = [route('home'), route('about'), route('services.index'), route('packages.index'), route('gallery'), route('reviews'), route('contact'), route('terms'), route('privacy'), route('refund'), route('blog.index')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url><loc><?php echo e($url); ?></loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url><loc><?php echo e(route('services.show', $service)); ?></loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url><loc><?php echo e(route('blog.show', $blog)); ?></loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\sitemap.blade.php ENDPATH**/ ?>
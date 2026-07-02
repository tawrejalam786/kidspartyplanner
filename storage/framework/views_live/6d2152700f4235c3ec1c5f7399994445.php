<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">About</span>
        <h1><?php echo e($page->title); ?></h1>
        <p>Birthday planning that is cheerful, organized, and easy to book.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="content-body"><?php echo $page->content; ?></div>
                <div class="why-list mt-4">
                    <div><i class="fa-solid fa-map"></i><span>Delhi NCR focused service coverage</span></div>
                    <div><i class="fa-solid fa-child-reaching"></i><span>Age-friendly activities and trained hosts</span></div>
                    <div><i class="fa-solid fa-credit-card"></i><span>Online payment and booking tracking</span></div>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="rounded-media" src="https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=1000&q=80" alt="Kids birthday party celebration" loading="lazy">
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\pages\about.blade.php ENDPATH**/ ?>
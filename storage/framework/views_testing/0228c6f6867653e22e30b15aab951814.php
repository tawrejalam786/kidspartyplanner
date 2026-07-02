<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Reviews</span>
        <h1>What parents say</h1>
        <p>Approved reviews from families who booked kids birthday activities.</p>
    </div>
</section>
<section class="section section-soft">
    <div class="container">
        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6 col-xl-4">
                    <div class="testimonial-card h-100">
                        <div class="stars"><?php for($i = 0; $i < $review->rating; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?></div>
                        <p><?php echo e($review->comment); ?></p>
                        <strong><?php echo e($review->customer_name); ?></strong>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12"><div class="empty-state">No approved reviews yet.</div></div>
            <?php endif; ?>
        </div>
        <div class="mt-4"><?php echo e($reviews->links()); ?></div>
    </div>
</section>

<?php if(auth()->guard()->check()): ?>
<section class="section">
    <div class="container">
        <div class="booking-panel mx-auto" style="max-width:760px">
            <h2>Share your experience</h2>
            <form action="<?php echo e(route('reviews.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="customer_name" value="<?php echo e(auth()->user()->name); ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Service</label><select class="form-select" name="service_id"><option value="">General review</option><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($service->id); ?>"><?php echo e($service->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div class="col-md-6"><label class="form-label">Rating</label><select class="form-select" name="rating"><?php for($i=5;$i>=1;$i--): ?><option value="<?php echo e($i); ?>"><?php echo e($i); ?> stars</option><?php endfor; ?></select></div>
                    <div class="col-12"><label class="form-label">Comment</label><textarea class="form-control" name="comment" rows="4" required></textarea></div>
                </div>
                <button class="btn btn-party mt-4">Submit Review</button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/pages/reviews.blade.php ENDPATH**/ ?>
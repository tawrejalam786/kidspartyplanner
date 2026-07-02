<?php $__env->startSection('content'); ?>
<section class="page-hero booking-hero">
    <div class="container">
        <span class="eyebrow">Easy booking</span>
        <h1>Your party, neatly planned.</h1>
        <p>Choose the city and see the exact local payment details before you confirm.</p>
    </div>
</section>
<section class="section booking-page">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-xl-8">
                <div class="booking-panel">
                    <?php if($service): ?>
                        <span class="badge soft-badge mb-2">Service selected</span>
                        <h2><?php echo e($service->title); ?></h2>
                    <?php elseif($package): ?>
                        <span class="badge soft-badge mb-2">Package selected</span>
                        <h2><?php echo e($package->title); ?></h2>
                    <?php else: ?>
                        <h2>Custom booking request</h2>
                    <?php endif; ?>
                    <?php echo $__env->make('partials.booking-form', compact('service', 'package', 'services', 'packages', 'cities'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <div class="col-xl-4">
                <aside class="booking-assurance">
                    <span class="mini-label">What happens next</span>
                    <h2>We hold the slot while you pay</h2>
                    <div class="assurance-item"><i class="fa-solid fa-phone-volume"></i><div><strong>Confirmation call</strong><span>Event details are verified by our coordinator.</span></div></div>
                    <div class="assurance-item"><i class="fa-solid fa-shield-heart"></i><div><strong>Secure payment</strong><span>City-specific Razorpay configuration and verification.</span></div></div>
                    <div class="assurance-item"><i class="fa-solid fa-calendar-check"></i><div><strong>Dashboard tracking</strong><span>Booking, status and payment history in one place.</span></div></div>
                </aside>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\booking\create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Contact</span>
        <h1>Tell us about the party</h1>
        <p>Share date, location, age group, and activities you are considering.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-panel">
                    <h2>Kids Party Planner</h2>
                    <p><i class="fa-solid fa-phone"></i> <?php echo e(\App\Models\Setting::getValue('site_phone', '+91 99999 99999')); ?></p>
                    <p><i class="fa-solid fa-envelope"></i> <?php echo e(\App\Models\Setting::getValue('site_email', 'hello@kidspartyplanner.test')); ?></p>
                    <p><i class="fa-solid fa-location-dot"></i> <?php echo e(\App\Models\Setting::getValue('service_area', 'Delhi NCR')); ?></p>
                    <a class="btn btn-success" target="_blank" rel="noopener" href="https://wa.me/<?php echo e(\App\Models\Setting::getValue('whatsapp_number', config('services.whatsapp.number'))); ?>?text=<?php echo e(urlencode('Hi Kids Party Planner, I need help planning a birthday party.')); ?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp Enquiry</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="booking-panel">
                    <h2>Send enquiry</h2>
                    <form action="<?php echo e(route('enquiries.store')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="source" value="Contact Page">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email"></div>
                            <div class="col-md-6"><label class="form-label">Service</label><select class="form-select" name="service_id"><option value="">Not sure yet</option><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($service->id); ?>"><?php echo e($service->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                            <div class="col-12"><label class="form-label">Subject</label><input class="form-control" name="subject" placeholder="Birthday party enquiry"></div>
                            <div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" required></textarea></div>
                        </div>
                        <button class="btn btn-party mt-4">Submit Enquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/pages/contact.blade.php ENDPATH**/ ?>
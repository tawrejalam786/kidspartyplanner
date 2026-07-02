<?php $__env->startSection('content'); ?>
<section class="auth-section vendor-register-section">
    <div class="auth-card vendor-register-card">
        <span class="eyebrow">Vendor network</span>
        <h1>Join Kids Party Planner</h1>
        <p class="section-lead mb-4">Register your local party service team. Admin will verify your profile before assigning bookings.</p>
        <form method="post" action="<?php echo e(route('vendors.register.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Business name</label><input class="form-control" name="business_name" value="<?php echo e(old('business_name')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Contact person</label><input class="form-control" name="contact_person" value="<?php echo e(old('contact_person')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?php echo e(old('phone')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?php echo e(old('email')); ?>" required></div>
                <div class="col-md-6">
                    <label class="form-label">Primary city</label>
                    <select class="form-select" name="city_id">
                        <option value="">Select current city</option>
                        <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($city->id); ?>" <?php if(old('city_id') == $city->id): echo 'selected'; endif; ?>><?php echo e($city->name); ?><?php echo e($city->state ? ', '.$city->state : ''); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Other city</label><input class="form-control" name="city" value="<?php echo e(old('city')); ?>" placeholder="Use if city is not listed"></div>
                <div class="col-md-6"><label class="form-label">State</label><input class="form-control" name="state" value="<?php echo e(old('state')); ?>" placeholder="Maharashtra, Delhi, Haryana"></div>
                <div class="col-md-6"><label class="form-label">Coverage areas</label><input class="form-control" name="coverage_areas" value="<?php echo e(old('coverage_areas')); ?>" placeholder="Dwarka, Rohini, Sector 62"></div>
                <div class="col-12"><label class="form-label">Full address</label><textarea class="form-control" name="address" rows="2"><?php echo e(old('address')); ?></textarea></div>
                <div class="col-12">
                    <label class="form-label">Services you can handle</label>
                    <select class="form-select" name="service_ids[]" multiple size="8" required>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($service->id); ?>" <?php if(in_array($service->id, old('service_ids', []))): echo 'selected'; endif; ?>><?php echo e($service->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple services.</small>
                </div>
                <div class="col-md-4"><label class="form-label">Account holder</label><input class="form-control" name="account_name" value="<?php echo e(old('account_name')); ?>"></div>
                <div class="col-md-4"><label class="form-label">Account number</label><input class="form-control" name="account_number" value="<?php echo e(old('account_number')); ?>"></div>
                <div class="col-md-4"><label class="form-label">IFSC</label><input class="form-control" name="ifsc" value="<?php echo e(old('ifsc')); ?>"></div>
                <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <div class="col-md-6"><label class="form-label">Confirm password</label><input class="form-control" type="password" name="password_confirmation" required></div>
            </div>
            <button class="btn btn-party w-100 mt-4">Submit vendor profile</button>
        </form>
        <p class="auth-switch">Already registered? <a href="<?php echo e(route('login')); ?>">Login</a></p>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\vendors\register.blade.php ENDPATH**/ ?>
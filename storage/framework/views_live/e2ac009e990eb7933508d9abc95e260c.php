<?php $__env->startSection('content'); ?>
<section class="auth-section">
    <div class="auth-card wide">
        <span class="eyebrow">Customer</span>
        <h1>Create account</h1>
        <a class="google-auth-button" href="<?php echo e(route('auth.google.redirect')); ?>"><i class="fa-brands fa-google"></i><span>Continue with Google</span></a>
        <div class="auth-divider"><span>or register with email</span></div>
        <form method="post" action="<?php echo e(route('register.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="<?php echo e(old('name')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?php echo e(old('phone')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?php echo e(old('email')); ?>" required></div>
                <div class="col-md-6"><label class="form-label">City</label><input class="form-control" name="city" value="<?php echo e(old('city')); ?>" placeholder="Delhi, Noida, Gurgaon"></div>
                <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <div class="col-md-6"><label class="form-label">Confirm Password</label><input class="form-control" type="password" name="password_confirmation" required></div>
            </div>
            <button class="btn btn-party w-100 mt-4" type="submit">Register</button>
        </form>
        <p class="auth-switch">Already registered? <a href="<?php echo e(route('login')); ?>">Login</a></p>
        <p class="auth-switch">Want to receive local party jobs? <a href="<?php echo e(route('vendors.register')); ?>">Register as vendor</a></p>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/auth/register.blade.php ENDPATH**/ ?>
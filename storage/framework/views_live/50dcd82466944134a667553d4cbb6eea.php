<?php $__env->startSection('content'); ?>
<section class="auth-section">
    <div class="auth-card">
        <span class="eyebrow"><?php echo e($isAdminLogin ?? false ? 'Admin' : 'Customer'); ?></span>
        <h1><?php echo e($isAdminLogin ?? false ? 'Admin Login' : 'Customer Login'); ?></h1>
        <?php if (! ($isAdminLogin ?? false)): ?>
            <a class="google-auth-button" href="<?php echo e(route('auth.google.redirect')); ?>"><i class="fa-brands fa-google"></i><span>Continue with Google</span></a>
            <div class="auth-divider"><span>or continue with email</span></div>
        <?php endif; ?>
        <form method="post" action="<?php echo e($isAdminLogin ?? false ? route('admin.login.store') : route('login.store')); ?>">
            <?php echo csrf_field(); ?>
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
            <label class="form-label mt-3">Password</label>
            <input class="form-control" type="password" name="password" required>
            <label class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="remember" value="1">
                <span class="form-check-label">Remember me</span>
            </label>
            <button class="btn btn-party w-100 mt-4" type="submit">Login</button>
        </form>
        <?php if (! ($isAdminLogin ?? false)): ?>
            <p class="auth-switch">New here? <a href="<?php echo e(route('register')); ?>">Create customer account</a></p>
            <p class="auth-switch">Party service provider? <a href="<?php echo e(route('vendors.register')); ?>">Register as vendor</a></p>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/auth/login.blade.php ENDPATH**/ ?>
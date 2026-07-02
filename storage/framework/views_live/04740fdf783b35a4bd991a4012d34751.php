<?php $__env->startSection('content'); ?>
<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-welcome">
            <div><span class="mini-label">Customer dashboard</span><h1>My profile</h1><p class="section-lead">Keep contact and venue details ready for checkout.</p></div>
            <a class="btn btn-outline-party" href="<?php echo e(route('dashboard')); ?>">Back to dashboard</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="dashboard-panel">
                    <form method="post" action="<?php echo e(route('dashboard.profile.update')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="profile-editor-head">
                            <div class="profile-avatar-preview">
                                <?php if($user->avatar_url): ?>
                                    <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>">
                                <?php else: ?>
                                    <i class="fa-regular fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="mini-label">Account details</span>
                                <h2><?php echo e($user->name); ?></h2>
                                <p><?php echo e($user->email); ?></p>
                                <?php if($user->google_id): ?><span class="google-account-badge"><i class="fa-brands fa-google"></i> Google login enabled</span><?php endif; ?>
                            </div>
                        </div>

                        <?php if($user->pending_email): ?>
                            <div class="dashboard-alert compact">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                                <div><strong>Email verification pending</strong><span>We sent a verification link to <?php echo e($user->pending_email); ?>.</span></div>
                                <button class="btn btn-outline-party btn-sm" form="resend-email-verification">Resend</button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input class="form-control" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                                <small class="form-text text-muted">Changing email requires verification before it becomes active.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile image</label>
                                <input class="form-control" type="file" name="avatar" accept="image/*">
                                <small class="form-text text-muted">Recommended 600 x 600 px. JPG, PNG or WebP, max 2 MB.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default city</label>
                                <input class="form-control" name="city" value="<?php echo e(old('city', $user->city)); ?>" placeholder="Delhi, Noida or Gurgaon">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Default address</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Full house, venue, sector and landmark"><?php echo e(old('address', $user->address)); ?></textarea>
                            </div>
                        </div>

                        <button class="btn btn-party mt-4">Save profile</button>
                    </form>

                    <?php if($user->pending_email): ?>
                        <form id="resend-email-verification" method="post" action="<?php echo e(route('dashboard.profile.email.resend')); ?>" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\dashboard\profile.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<section class="dashboard-shell vendor-dashboard-shell">
    <div class="container">
        <div class="dashboard-welcome">
            <div><span class="mini-label">Vendor dashboard</span><h1><?php echo e($vendor->business_name); ?></h1><p class="section-lead"><?php echo e($vendor->city ?: 'Service city'); ?> &middot; <?php echo e($vendor->status); ?></p></div>
            <form method="post" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="btn btn-outline-party">Logout</button></form>
        </div>

        <?php if($vendor->status !== 'Approved'): ?>
            <div class="dashboard-alert">
                <i class="fa-solid fa-hourglass-half"></i>
                <div><strong>Approval pending</strong><span>Admin needs to approve this vendor profile before live job assignment starts.</span></div>
            </div>
        <?php endif; ?>

        <div class="dashboard-stats">
            <div class="dashboard-stat"><i class="fa-solid fa-clipboard-check"></i><span>Total jobs</span><strong><?php echo e($stats['assigned']); ?></strong></div>
            <div class="dashboard-stat"><i class="fa-solid fa-person-running"></i><span>Active jobs</span><strong><?php echo e($stats['active']); ?></strong></div>
            <div class="dashboard-stat"><i class="fa-solid fa-wallet"></i><span>Available</span><strong>&#8377;<?php echo e(number_format((float) $stats['available'])); ?></strong></div>
            <div class="dashboard-stat"><i class="fa-regular fa-clock"></i><span>Pending</span><strong>&#8377;<?php echo e(number_format((float) $stats['pending'])); ?></strong></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head"><h2>Assigned jobs</h2><span class="status-badge"><?php echo e($vendor->status); ?></span></div>
                    <div class="table-responsive dashboard-table">
                        <table class="table align-middle">
                            <thead><tr><th>Booking</th><th>Event</th><th>Customer</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($assignment->booking->item_title); ?></strong><br><small><?php echo e($assignment->booking->booking_no); ?> &middot; <?php echo e($assignment->booking->city?->name ?? $assignment->booking->cityPaymentSetting?->city); ?></small></td>
                                        <td><?php echo e($assignment->booking->event_date->format('d M Y')); ?><br><small><?php echo e(\Illuminate\Support\Str::of($assignment->booking->event_time)->substr(0, 5)); ?></small></td>
                                        <td><?php echo e($assignment->booking->customer_name); ?><br><small><?php echo e($assignment->booking->customer_phone); ?></small></td>
                                        <td>&#8377;<?php echo e(number_format((float) $assignment->vendor_earning)); ?></td>
                                        <td><span class="status-badge"><?php echo e($assignment->status); ?></span></td>
                                        <td>
                                            <div class="vendor-job-actions">
                                                <?php if($assignment->status === 'Assigned'): ?>
                                                    <form method="post" action="<?php echo e(route('vendor.assignments.accept', $assignment)); ?>"><?php echo csrf_field(); ?><button class="btn btn-outline-party btn-sm">Accept</button></form>
                                                <?php endif; ?>
                                                <?php if(in_array($assignment->status, ['Assigned', 'Accepted', 'In Progress'], true)): ?>
                                                    <form method="post" action="<?php echo e(route('vendor.assignments.complete', $assignment)); ?>"><?php echo csrf_field(); ?><button class="btn btn-party btn-sm">Complete</button></form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="text-center py-5">No jobs assigned yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3"><?php echo e($assignments->links()); ?></div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="dashboard-panel mb-4">
                    <div class="dashboard-panel-head"><h2>Withdraw earnings</h2><i class="fa-solid fa-money-bill-transfer"></i></div>
                    <form method="post" action="<?php echo e(route('vendor.withdrawals.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <label class="form-label">Amount</label>
                        <input class="form-control" type="number" name="amount" min="100" max="<?php echo e((float) $stats['available']); ?>" value="<?php echo e(old('amount')); ?>" required>
                        <button class="btn btn-party w-100 mt-3" <?php if((float) $stats['available'] < 100): echo 'disabled'; endif; ?>>Request withdrawal</button>
                    </form>
                </div>
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head"><h2>Service coverage</h2><i class="fa-solid fa-list-check"></i></div>
                    <div class="vendor-service-list">
                        <?php $__currentLoopData = $vendor->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($service->title); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6"><div class="dashboard-panel"><div class="dashboard-panel-head"><h2>Recent earnings</h2></div><?php $__empty_1 = true; $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="admin-mini"><strong>&#8377;<?php echo e(number_format((float) $earning->net_amount)); ?> &middot; <?php echo e($earning->status); ?></strong><span><?php echo e($earning->booking->booking_no); ?> &middot; Commission &#8377;<?php echo e(number_format((float) $earning->commission_amount)); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="empty-state py-4">No earnings yet.</div><?php endif; ?></div></div>
            <div class="col-lg-6"><div class="dashboard-panel"><div class="dashboard-panel-head"><h2>Withdrawal history</h2></div><?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="admin-mini"><strong>&#8377;<?php echo e(number_format((float) $withdrawal->amount)); ?> &middot; <?php echo e($withdrawal->status); ?></strong><span><?php echo e($withdrawal->payout_reference ?: 'Awaiting payout reference'); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="empty-state py-4">No withdrawals yet.</div><?php endif; ?></div></div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/vendor/dashboard.blade.php ENDPATH**/ ?>
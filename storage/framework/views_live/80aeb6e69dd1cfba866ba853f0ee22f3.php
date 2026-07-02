<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $icons = ['Bookings' => 'fa-ticket', 'Today Bookings' => 'fa-calendar-day', 'Upcoming Bookings' => 'fa-calendar-check', 'Confirmed Bookings' => 'fa-circle-check', 'Cancelled Bookings' => 'fa-ban', 'Pending Payments' => 'fa-clock', 'Customers' => 'fa-users', 'Vendors' => 'fa-handshake', 'Total Revenue' => 'fa-indian-rupee-sign'];
    $statusTotal = max(1, $statusCounts->sum());
    $maxCityBookings = max(1, (int) $cityStats->max('bookings_count'));
?>
<div class="admin-stats">
    <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="admin-stat"><span><i class="fa-solid <?php echo e($icons[$label] ?? 'fa-chart-simple'); ?> me-2"></i><?php echo e($label); ?></span><strong><?php if($label === 'Total Revenue'): ?>&#8377;<?php endif; ?><?php echo e(number_format((float) $value)); ?></strong></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="admin-card h-100 event-focus-card">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Call today</span><h2 class="mb-0">Today's events</h2></div><a href="<?php echo e(route('admin.resources.index', 'bookings', ['search' => today()->format('Y-m-d')])); ?>" class="icon-link"><i class="fa-solid fa-arrow-right"></i></a></div>
            <?php $__empty_1 = true; $__currentLoopData = $todayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="admin-mini event-focus-row"><strong><?php echo e($booking->item_title); ?></strong><span><?php echo e(\Illuminate\Support\Str::of($booking->event_time)->substr(0,5)); ?> &middot; <?php echo e($booking->customer_name); ?> &middot; <?php echo e($booking->customer_phone); ?></span><small><?php echo e($booking->full_address ?: $booking->location); ?></small></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state py-4">No events scheduled for today.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card h-100 event-focus-card">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Next 7 days</span><h2 class="mb-0">Upcoming follow-ups</h2></div><a href="<?php echo e(route('admin.resources.index', 'bookings')); ?>" class="btn btn-outline-party btn-sm">All bookings</a></div>
            <?php $__empty_1 = true; $__currentLoopData = $upcomingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="admin-mini event-focus-row"><strong><?php echo e($booking->event_date->format('d M')); ?> &middot; <?php echo e($booking->item_title); ?></strong><span><?php echo e($booking->customer_name); ?> &middot; <?php echo e($booking->customer_phone); ?> &middot; <?php echo e($booking->city?->name ?? $booking->cityPaymentSetting?->city ?? '-'); ?></span></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state py-4">No upcoming follow-ups this week.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-8">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Live operations</span><h2 class="mb-0">Recent bookings</h2></div><a href="<?php echo e(route('admin.resources.index', 'bookings')); ?>" class="btn btn-outline-party btn-sm">View all</a></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Booking</th><th>Customer</th><th>City</th><th>Event</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody><?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><strong><?php echo e($booking->item_title); ?></strong><br><small class="text-muted"><?php echo e($booking->booking_no); ?></small></td><td><?php echo e($booking->customer_name); ?></td><td><?php echo e($booking->city?->name ?? $booking->cityPaymentSetting?->city ?? '-'); ?></td><td><?php echo e($booking->event_date->format('d M Y')); ?></td><td><span class="status-badge"><?php echo e($booking->workflow_status); ?></span></td><td>&#8377;<?php echo e(number_format((float) $booking->total_amount)); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="text-center py-4">No bookings yet.</td></tr><?php endif; ?></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="admin-card h-100">
            <span class="mini-label mb-1">Booking pipeline</span><h2>Status overview</h2>
            <div class="admin-status-grid mt-3"><?php $__currentLoopData = $statusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="admin-status-item"><span><?php echo e($status); ?></span><strong><?php echo e($count); ?></strong><div class="progress mt-2" style="height:4px"><div class="progress-bar" style="width:<?php echo e(($count / $statusTotal) * 100); ?>%; background:var(--<?php echo e($status === 'Cancelled' ? 'coral' : 'mint-dark'); ?>)"></div></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-7">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Demand by location</span><h2 class="mb-0">City performance</h2></div><a href="<?php echo e(route('admin.resources.index', 'city-payments')); ?>" class="btn btn-outline-party btn-sm">Payment rules</a></div>
            <div class="city-performance"><?php $__currentLoopData = $cityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="city-performance-row"><strong><?php echo e($city->city); ?></strong><div class="progress"><div class="progress-bar" style="width:<?php echo e(($city->bookings_count / $maxCityBookings) * 100); ?>%"></div></div><span><?php echo e($city->bookings_count); ?> bookings</span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><hr><span class="mini-label mb-2">Top selling services</span><?php $__currentLoopData = $topServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="admin-mini"><strong><?php echo e($service->title); ?></strong><span><?php echo e($service->bookings_count); ?> direct bookings</span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-2"><div><span class="mini-label mb-1">Needs attention</span><h2 class="mb-0">New enquiries</h2></div><a href="<?php echo e(route('admin.resources.index', 'enquiries')); ?>" class="icon-link"><i class="fa-solid fa-arrow-right"></i></a></div>
            <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="admin-mini"><strong><?php echo e($enquiry->name); ?></strong><span><?php echo e($enquiry->phone); ?> &middot; <?php echo e($enquiry->status); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="empty-state py-4">No new enquiries.</div><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>
<h2>Your booking is confirmed</h2>
<p>Hi <?php echo e($booking->customer_name); ?>,</p>
<p>Payment has been received for booking <strong><?php echo e($booking->booking_no); ?></strong>.</p>
<p><strong>Experience:</strong> <?php echo e($booking->item_title); ?></p>
<p><strong>Event:</strong> <?php echo e($booking->event_date->format('d M Y')); ?> at <?php echo e(\Illuminate\Support\Str::of($booking->event_time)->substr(0, 5)); ?></p>
<p><strong>Address:</strong> <?php echo e($booking->full_address ?: $booking->location); ?></p>
<p><strong>Paid now:</strong> Rs. <?php echo e(number_format((float) $booking->latestPayment?->amount)); ?></p>
<p><strong>Payment status:</strong> <?php echo e($booking->payment_status); ?></p>
<p>Your invoice is attached. Our team will contact you before the event date for final coordination.</p>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\emails\payment-confirmed.blade.php ENDPATH**/ ?>
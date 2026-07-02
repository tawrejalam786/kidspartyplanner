<h2>New booking received</h2>
<p><strong>Booking:</strong> <?php echo e($booking->booking_no); ?></p>
<p><strong>Customer:</strong> <?php echo e($booking->customer_name); ?> (<?php echo e($booking->customer_phone); ?>)</p>
<p><strong>Email:</strong> <?php echo e($booking->customer_email); ?></p>
<p><strong>Item:</strong> <?php echo e($booking->item_title); ?></p>
<p><strong>Date:</strong> <?php echo e($booking->event_date->format('d M Y')); ?> at <?php echo e(\Illuminate\Support\Str::of($booking->event_time)->substr(0, 5)); ?></p>
<p><strong>Address:</strong> <?php echo e($booking->full_address ?: $booking->location); ?></p>
<p><strong>Kids:</strong> <?php echo e($booking->number_of_kids); ?></p>
<p><strong>Total:</strong> Rs. <?php echo e(number_format((float) $booking->total_amount)); ?></p>
<p><strong>Payable:</strong> Rs. <?php echo e(number_format((float) $booking->payable_amount)); ?></p>
<?php if($booking->message): ?>
    <p><strong>Message:</strong> <?php echo e($booking->message); ?></p>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\emails\booking-received.blade.php ENDPATH**/ ?>
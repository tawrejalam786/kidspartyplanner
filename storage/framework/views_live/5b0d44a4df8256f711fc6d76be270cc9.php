<h2>Verify your new email</h2>
<p>Hi <?php echo e($user->name); ?>,</p>
<p>We received a request to change your Kids Party Planner account email to <strong><?php echo e($user->pending_email); ?></strong>.</p>
<p><a href="<?php echo e($verificationUrl); ?>">Verify email address</a></p>
<p>This link is valid for 24 hours. If you did not request this change, you can ignore this email.</p>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\emails\verify-email-change.blade.php ENDPATH**/ ?>
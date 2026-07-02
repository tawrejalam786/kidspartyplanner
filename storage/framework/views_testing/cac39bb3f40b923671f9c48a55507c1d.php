<div class="row g-3 g-md-4">
    <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-6 col-xl-4">
            <?php echo $__env->make('partials.service-card', ['service' => $service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="empty-state">No services matched these filters.</div>
        </div>
    <?php endif; ?>
</div>
<div class="mt-4 ajax-pagination">
    <?php echo e($services->links()); ?>

</div>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/services/_cards.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', $config['title']); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 flex-wrap">
        <div><h2 class="mb-0"><?php echo e($config['title']); ?></h2><small class="text-muted">Search records, then open the edit action.</small></div>
        <a href="<?php echo e(route('admin.resources.create', $resource)); ?>" class="btn btn-party btn-sm"><i class="fa-solid fa-plus"></i> Add <?php echo e($config['singular']); ?></a>
    </div>
    <form class="admin-search-form" method="get" action="<?php echo e(route('admin.resources.index', $resource)); ?>">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="search" value="<?php echo e($search); ?>" placeholder="Search <?php echo e(strtolower($config['title'])); ?>" aria-label="Search <?php echo e($config['title']); ?>">
        <?php if($search !== ''): ?><a href="<?php echo e(route('admin.resources.index', $resource)); ?>" aria-label="Clear search"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
        <button class="btn btn-dark btn-sm" type="submit">Search</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <?php $__currentLoopData = $config['columns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e(\Illuminate\Support\Str::headline(str_replace('.', ' ', $column))); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <?php $__currentLoopData = $config['columns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($value = data_get($item, $column)); ?>
                            <td>
                                <?php if(in_array($column, ['image', 'path'], true) && $value): ?>
                                    <img class="admin-table-thumb" src="<?php echo e(\Illuminate\Support\Str::startsWith($value, ['http://', 'https://']) ? $value : asset('storage/'.$value)); ?>" alt="">
                                <?php elseif(is_bool($value)): ?>
                                    <span class="badge <?php echo e($value ? 'text-bg-success' : 'text-bg-secondary'); ?>"><?php echo e($value ? 'Yes' : 'No'); ?></span>
                                <?php elseif($value instanceof \Carbon\CarbonInterface): ?>
                                    <?php echo e($value->format('d M Y')); ?>

                                <?php elseif(is_array($value)): ?>
                                    <?php echo e(\Illuminate\Support\Str::limit(json_encode($value), 60)); ?>

                                <?php else: ?>
                                    <?php echo e(\Illuminate\Support\Str::limit((string) $value, 80)); ?>

                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-end">
                            <a class="btn btn-outline-party btn-sm" href="<?php echo e(route('admin.resources.edit', [$resource, $item->id])); ?>"><i class="fa-solid fa-pen"></i></a>
                            <form action="<?php echo e(route('admin.resources.destroy', [$resource, $item->id])); ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e(count($config['columns']) + 1); ?>" class="text-center py-5"><?php echo e($search !== '' ? 'No records matched “'.$search.'”.' : 'No records found.'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?php echo e($items->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views/admin/resources/index.blade.php ENDPATH**/ ?>
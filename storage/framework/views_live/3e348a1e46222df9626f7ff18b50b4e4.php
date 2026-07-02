<?php $__env->startSection('title', ($item ? 'Edit ' : 'Add ').$config['singular']); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card">
    <form method="post" enctype="multipart/form-data" action="<?php echo e($item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource)); ?>">
        <?php echo csrf_field(); ?>
        <?php if($item): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row g-3">
            <?php $__currentLoopData = $config['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $name = $field['name'];
                    $type = $field['type'];
                    $value = old($name, $item ? data_get($item, $name) : null);
                    if ($type === 'password') {
                        $value = null;
                    }
                    if (is_array($value)) {
                        $value = json_encode($value, JSON_PRETTY_PRINT);
                    }
                    if ($value instanceof \Carbon\CarbonInterface) {
                        $value = in_array($type, ['datetime'], true) ? $value->format('Y-m-d\TH:i') : $value->format('Y-m-d');
                    }
                ?>
                <div class="<?php echo e(in_array($type, ['textarea', 'json'], true) ? 'col-12' : 'col-md-6'); ?>">
                    <?php if($type === 'checkbox'): ?>
                        <label class="form-check admin-check">
                            <input class="form-check-input" type="checkbox" name="<?php echo e($name); ?>" value="1" <?php if(old($name, $item ? (bool) data_get($item, $name) : ($field['default'] ?? true))): echo 'checked'; endif; ?>>
                            <span class="form-check-label"><?php echo e($field['label']); ?></span>
                        </label>
                    <?php elseif($type === 'select'): ?>
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <select class="form-select" name="<?php echo e($name); ?>">
                            <?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($optionValue); ?>" <?php if((string) $value === (string) $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php elseif($type === 'multiselect'): ?>
                        <?php $selectedValues = old($name, $item ? $item->{$field['relation']}->pluck('id')->map(fn($id)=>(string)$id)->all() : []); ?>
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <select class="form-select" name="<?php echo e($name); ?>[]" multiple size="6">
                            <?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($optionValue); ?>" <?php if(in_array((string)$optionValue, array_map('strval', $selectedValues ?? []), true)): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php elseif($type === 'textarea' || $type === 'json'): ?>
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <textarea class="form-control" name="<?php echo e($name); ?>" rows="<?php echo e($type === 'json' ? 7 : 4); ?>"><?php echo e($value); ?></textarea>
                    <?php elseif($type === 'file'): ?>
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <?php
                            $recommendedSize = match($field['folder'] ?? '') {
                                'services' => '1200 x 600 px',
                                'banners' => '1600 x 700 px',
                                'categories', 'subcategories', 'blogs' => '900 x 700 px',
                                'addons' => '600 x 600 px',
                                'gallery' => '1200 x 900 px',
                                default => '1200 x 800 px',
                            };
                            $currentImagePath = null;
                            if ($item) {
                                if (($field['virtual'] ?? false) && $resource === 'services' && $name === 'primary_image') {
                                    $currentImagePath = optional($item->primaryImage)->path ?: optional($item->images->first())->path;
                                } elseif (! ($field['virtual'] ?? false)) {
                                    $currentImagePath = $value;
                                }
                            }
                            $currentImageUrl = $currentImagePath
                                ? (\Illuminate\Support\Str::startsWith($currentImagePath, ['http://','https://']) ? $currentImagePath : asset('storage/'.$currentImagePath))
                                : (($field['virtual'] ?? false) && $item ? ($item->image_url ?? null) : null);
                        ?>
                        <?php if($item && $currentImageUrl): ?>
                            <div class="admin-current-image">
                                <span>Current image</span>
                                <a href="<?php echo e($currentImageUrl); ?>" target="_blank" rel="noopener">
                                    <img class="admin-preview" src="<?php echo e($currentImageUrl); ?>" alt="<?php echo e($field['label']); ?>">
                                </a>
                                <?php if($currentImagePath): ?>
                                    <small><?php echo e($currentImagePath); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <input class="form-control" type="file" name="<?php echo e($name); ?>" accept="image/*" <?php if(($field['required'] ?? false) && ! $item): ?> required <?php endif; ?>>
                        <small class="form-text text-muted">
                            Recommended <?php echo e($recommendedSize); ?>. JPG, PNG or WebP, maximum 2 MB.
                            <?php if($item): ?> Leave blank to keep current image. <?php endif; ?>
                        </small>
                        <?php if($field['help'] ?? false): ?>
                            <small class="form-text text-muted d-block"><?php echo e($field['help']); ?></small>
                        <?php endif; ?>
                        <?php if($item && ! $currentImageUrl): ?>
                            <div class="admin-current-image is-empty">
                                <span>No current image found</span>
                                <small>Upload an image to add one.</small>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <label class="form-label"><?php echo e($field['label']); ?></label>
                        <input class="form-control" type="<?php echo e($type === 'datetime' ? 'datetime-local' : $type); ?>" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" <?php if($field['required'] ?? false): ?> required <?php endif; ?>>
                        <?php if($field['help'] ?? false): ?>
                            <small class="form-text text-muted"><?php echo e($field['help']); ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-party" type="submit">Save <?php echo e($config['singular']); ?></button>
            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.resources.index', $resource)); ?>">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\admin\resources\form_v2.blade.php ENDPATH**/ ?>
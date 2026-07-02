<?php $__env->startSection('content'); ?>
<section class="page-hero compact-hero service-list-hero">
    <div class="container">
        <span class="eyebrow"><?php echo e($selectedCity?->name); ?> marketplace</span>
        <h1><?php echo e(isset($subcategoryPage) ? $subcategoryPage->name : (isset($categoryPage) ? $categoryPage->name : 'Party services & designs')); ?></h1>
        <p><?php echo e(isset($subcategoryPage) ? $subcategoryPage->description : (isset($categoryPage) ? $categoryPage->description : 'Compare activities, entertainment and decorations with local pricing and availability.')); ?></p>
    </div>
</section>

<section class="section service-list-section">
    <div class="container">
        <div class="mobile-filter-toolbar d-lg-none">
            <div>
                <?php if(request('search')): ?><small>Results for</small><strong data-mobile-result-query>&ldquo;<?php echo e(request('search')); ?>&rdquo;</strong><?php else: ?><small>Explore services in</small><strong data-mobile-result-query><?php echo e($selectedCity?->name); ?></strong><?php endif; ?>
                <span><b id="mobile-service-count"><?php echo e($services->total()); ?></b> services found</span>
            </div>
            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters"><i class="fa-solid fa-sliders"></i> Filters</button>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 d-none d-lg-block">
                <?php echo $__env->make('services._filter-form', ['filterId' => 'service-filter', 'filterClass' => 'filter-panel desktop-service-filter'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="col-lg-9">
                <div class="listing-top d-none d-lg-flex">
                    <div><strong id="service-count"><?php echo e($services->total()); ?></strong> services available in <?php echo e($selectedCity?->name); ?></div>
                    <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-party btn-sm"><i class="fa-solid fa-bag-shopping"></i> Party cart</a>
                </div>
                <div id="service-results">
                    <?php echo $__env->make('services._cards', ['services' => $services, 'selectedCity' => $selectedCity], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="offcanvas offcanvas-bottom mobile-filter-offcanvas" tabindex="-1" id="mobileFilters" aria-labelledby="mobileFiltersLabel">
    <div class="offcanvas-header">
        <div><span class="mini-label">Narrow the list</span><h2 id="mobileFiltersLabel">Filters & sorting</h2></div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php echo $__env->make('services._filter-form', ['filterId' => 'service-filter-mobile', 'filterClass' => 'mobile-filter-form'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\services\index.blade.php ENDPATH**/ ?>
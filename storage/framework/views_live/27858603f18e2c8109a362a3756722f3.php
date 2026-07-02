<form id="<?php echo e($filterId); ?>" class="service-filter-form <?php echo e($filterClass ?? ''); ?>" action="<?php echo e(route('services.index')); ?>">
    <label class="form-label">Search</label>
    <input class="form-control" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Magic, slime, games">

    <label class="form-label mt-3">City</label>
    <select class="form-select" name="city">
        <?php $__currentLoopData = $activeCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($city->slug); ?>" <?php if($selectedCity?->id === $city->id): echo 'selected'; endif; ?>><?php echo e($city->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <label class="form-label mt-3">Category</label>
    <select class="form-select" name="category">
        <option value="">All Categories</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->id); ?>" <?php if(request('category') == $category->id || (isset($categoryPage) && $categoryPage->id === $category->id)): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <label class="form-label mt-3">Subcategory</label>
    <select class="form-select" name="subcategory">
        <option value="">All subcategories</option>
        <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($subcategory->id); ?>" <?php if(request('subcategory') == $subcategory->id || (isset($subcategoryPage) && $subcategoryPage->id === $subcategory->id)): echo 'selected'; endif; ?>><?php echo e($subcategory->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <label class="form-label mt-3">Max Price</label>
    <input class="form-range" type="range" name="max_price" min="500" max="25000" step="500" value="<?php echo e(request('max_price', 25000)); ?>" data-price-range>
    <div class="small text-muted">Up to &#8377;<span data-price-output><?php echo e(request('max_price', 25000)); ?></span></div>

    <label class="form-label mt-3">Minimum Rating</label>
    <select class="form-select" name="rating">
        <option value="">Any rating</option>
        <option value="4" <?php if(request('rating') == 4): echo 'selected'; endif; ?>>4+ stars</option>
        <option value="4.5" <?php if(request('rating') == 4.5): echo 'selected'; endif; ?>>4.5+ stars</option>
    </select>

    <label class="form-label mt-3">Sort</label>
    <select class="form-select" name="sort">
        <option value="">Newest</option>
        <option value="price_low" <?php if(request('sort') === 'price_low'): echo 'selected'; endif; ?>>Price low to high</option>
        <option value="price_high" <?php if(request('sort') === 'price_high'): echo 'selected'; endif; ?>>Price high to low</option>
        <option value="rating" <?php if(request('sort') === 'rating'): echo 'selected'; endif; ?>>Rating</option>
        <option value="trending" <?php if(request('sort') === 'trending'): echo 'selected'; endif; ?>>Trending</option>
    </select>

    <div class="filter-actions">
        <a class="btn btn-light border" href="<?php echo e(route('services.index', ['city' => $selectedCity?->slug])); ?>">Reset</a>
        <button class="btn btn-party" type="submit"><i class="fa-solid fa-sliders"></i> Show services</button>
    </div>
</form>
<?php /**PATH C:\xampp\htdocs\kids-party-planner\resources\views\services\_filter-form.blade.php ENDPATH**/ ?>
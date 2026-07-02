<form id="{{ $filterId }}" class="service-filter-form {{ $filterClass ?? '' }}" action="{{ route('services.index') }}">
    <label class="form-label">Search</label>
    <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Magic, slime, games">

    <label class="form-label mt-3">City</label>
    <select class="form-select" name="city">
        @foreach($activeCities as $city)
            <option value="{{ $city->slug }}" @selected($selectedCity?->id === $city->id)>{{ $city->name }}</option>
        @endforeach
    </select>

    <label class="form-label mt-3">Category</label>
    <select class="form-select" name="category">
        <option value="">All Categories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id || (isset($categoryPage) && $categoryPage->id === $category->id))>{{ $category->name }}</option>
        @endforeach
    </select>

    <label class="form-label mt-3">Subcategory</label>
    <select class="form-select" name="subcategory">
        <option value="">All subcategories</option>
        @foreach($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}" @selected(request('subcategory') == $subcategory->id || (isset($subcategoryPage) && $subcategoryPage->id === $subcategory->id))>{{ $subcategory->name }}</option>
        @endforeach
    </select>

    <label class="form-label mt-3">Max Price</label>
    <input class="form-range" type="range" name="max_price" min="500" max="25000" step="500" value="{{ request('max_price', 25000) }}" data-price-range>
    <div class="small text-muted">Up to &#8377;<span data-price-output>{{ request('max_price', 25000) }}</span></div>

    <label class="form-label mt-3">Minimum Rating</label>
    <select class="form-select" name="rating">
        <option value="">Any rating</option>
        <option value="4" @selected(request('rating') == 4)>4+ stars</option>
        <option value="4.5" @selected(request('rating') == 4.5)>4.5+ stars</option>
    </select>

    <label class="form-label mt-3">Sort</label>
    <select class="form-select" name="sort">
        <option value="">Newest</option>
        <option value="price_low" @selected(request('sort') === 'price_low')>Price low to high</option>
        <option value="price_high" @selected(request('sort') === 'price_high')>Price high to low</option>
        <option value="rating" @selected(request('sort') === 'rating')>Rating</option>
        <option value="trending" @selected(request('sort') === 'trending')>Trending</option>
    </select>

    <div class="filter-actions">
        <a class="btn btn-light border" href="{{ route('services.index', ['city' => $selectedCity?->slug]) }}">Reset</a>
        <button class="btn btn-party" type="submit"><i class="fa-solid fa-sliders"></i> Show services</button>
    </div>
</form>

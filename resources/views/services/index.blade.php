@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero service-list-hero">
    <div class="container">
        <span class="eyebrow">{{ $selectedCity?->name }} marketplace</span>
        <h1>{{ isset($subcategoryPage) ? $subcategoryPage->name : (isset($categoryPage) ? $categoryPage->name : 'Party services & designs') }}</h1>
        <p>{{ isset($subcategoryPage) ? $subcategoryPage->description : (isset($categoryPage) ? $categoryPage->description : 'Compare activities, entertainment and decorations with local pricing and availability.') }}</p>
    </div>
</section>

<section class="section service-list-section">
    <div class="container">
        <div class="mobile-filter-toolbar d-lg-none">
            <div>
                @if(request('search'))<small>Results for</small><strong data-mobile-result-query>&ldquo;{{ request('search') }}&rdquo;</strong>@else<small>Explore services in</small><strong data-mobile-result-query>{{ $selectedCity?->name }}</strong>@endif
                <span><b id="mobile-service-count">{{ $services->total() }}</b> services found</span>
            </div>
            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters"><i class="fa-solid fa-sliders"></i> Filters</button>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 d-none d-lg-block">
                @include('services._filter-form', ['filterId' => 'service-filter', 'filterClass' => 'filter-panel desktop-service-filter'])
            </div>
            <div class="col-lg-9">
                <div class="listing-top d-none d-lg-flex">
                    <div><strong id="service-count">{{ $services->total() }}</strong> services available in {{ $selectedCity?->name }}</div>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-party btn-sm"><i class="fa-solid fa-bag-shopping"></i> Party cart</a>
                </div>
                <div id="service-results">
                    @include('services._cards', ['services' => $services, 'selectedCity' => $selectedCity])
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
        @include('services._filter-form', ['filterId' => 'service-filter-mobile', 'filterClass' => 'mobile-filter-form'])
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<section class="detail-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <img class="detail-main-image" src="{{ $package->image_url }}" alt="{{ $package->title }}" loading="lazy">
                <div class="detail-content">
                    <span class="badge soft-badge">Party Package</span>
                    <h1>{{ $package->title }}</h1>
                    <div class="detail-meta"><span><i class="fa-solid fa-clock"></i> {{ $package->duration }}</span><span><i class="fa-solid fa-location-dot"></i> Delhi NCR</span></div>
                    <div class="detail-price">Rs. {{ number_format($package->effective_price) }} @if($package->discount_price)<del>Rs. {{ number_format((float) $package->price) }}</del>@endif</div>
                    <p>{{ $package->description }}</p>
                    <h3>Services Included</h3>
                    <ul class="check-list">@foreach($package->services ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul>
                    <h3>Package Inclusions</h3>
                    <ul class="check-list">@foreach($package->inclusions ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="booking-panel">
                    <span class="eyebrow">Book package</span>
                    <h2>{{ $package->title }}</h2>
                    @include('partials.booking-form', ['package' => $package])
                </div>
            </div>
        </div>
    </div>
</section>
@if($relatedPackages->isNotEmpty())
<section class="section section-soft">
    <div class="container">
        <div class="section-heading"><span>More packages</span><h2>Other ready plans</h2></div>
        <div class="row g-4">@foreach($relatedPackages as $related)<div class="col-md-4">@include('partials.package-card', ['package' => $related])</div>@endforeach</div>
    </div>
</section>
@endif
@endsection

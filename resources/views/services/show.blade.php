@extends('layouts.app')

@section('content')
<section class="detail-section">
    <div class="container">
        <nav class="detail-breadcrumb">
            <a href="{{ route('home') }}">Home</a><i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('categories.show', $service->category) }}">{{ $service->category->name }}</a>
            @if($service->subcategory)<i class="fa-solid fa-chevron-right"></i><a href="{{ route('subcategories.show', $service->subcategory) }}">{{ $service->subcategory->name }}</a>@endif
        </nav>
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="swiper detail-swiper"><div class="swiper-wrapper">@foreach($service->images as $image)<div class="swiper-slide"><img src="{{ $image->url }}" alt="{{ $image->alt_text ?: $service->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}"></div>@endforeach</div><!-- Left Right Arrows -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div><div class="swiper-pagination"></div></div>
                <div class="detail-content">
                    <div class="d-flex align-items-center gap-2 flex-wrap"><span class="badge soft-badge">{{ $service->category->name }}</span>@if($service->trending)<span class="badge text-bg-warning">Trending</span>@endif</div>
                    <h1>{{ $service->title }}</h1>
                    <div class="detail-meta"><span><i class="fa-solid fa-star"></i> {{ $service->rating }} ({{ $service->total_reviews }} reviews)</span><span><i class="fa-regular fa-clock"></i> {{ $service->duration }}</span><span><i class="fa-solid fa-location-dot"></i> {{ $selectedCity?->name }}</span></div>
                    <div class="detail-price"><small>Starting price in {{ $selectedCity?->name }}</small><strong>&#8377;{{ number_format($displayPrice) }}</strong>@if($displayPrice < (float) $service->price)<del>&#8377;{{ number_format((float) $service->price) }}</del>@endif</div>
                    <p class="detail-lead">{{ $service->description }}</p>
                    <div class="detail-fact-grid"><div><i class="fa-solid fa-child-reaching"></i><span>Age group<strong>{{ $service->age_group ?: 'All kids' }}</strong></span></div><div><i class="fa-solid fa-people-group"></i><span>Kids capacity<strong>Up to {{ $service->kids_capacity ?: 25 }}</strong></span></div><div><i class="fa-solid fa-city"></i><span>Available in<strong>{{ $service->availableCities->where('pivot.is_available', true)->pluck('name')->join(', ') ?: 'Delhi NCR' }}</strong></span></div></div>
                    <div class="detail-info-grid">
                        <section><h2><i class="fa-solid fa-check"></i> What is included</h2><ul class="check-list">@foreach($service->inclusions ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul></section>
                        <section><h2><i class="fa-solid fa-xmark"></i> Not included</h2><ul class="cross-list">@foreach($service->exclusions ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul></section>
                        <section><h2><i class="fa-solid fa-clipboard-check"></i> Customer requirements</h2><ul class="check-list">@foreach($service->requirements ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul></section>
                        <section><h2><i class="fa-solid fa-calendar-xmark"></i> Cancellation</h2><p>{{ $service->cancellation_policy }}</p></section>
                    </div>
                    @if($service->terms)<div class="detail-terms"><h2>Service terms</h2><p>{{ $service->terms }}</p></div>@endif
                    @if($serviceFaqs->isNotEmpty())
                        <h2 class="mt-5">Service questions</h2>
                        <div class="accordion" id="serviceFaq">
                            @foreach($serviceFaqs as $faq)
                                <div class="accordion-item">
                                    <h3 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#serviceFaq{{ $loop->index }}">{{ $faq['question'] }}</button></h3>
                                    <div id="serviceFaq{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#serviceFaq"><div class="accordion-body">{{ $faq['answer'] }}</div></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-5">
                <aside class="service-booking-sidebar">
                    <div class="booking-panel">
                        <div class="service-booking-head"><div><span class="mini-label">Reserve this experience</span><h2>&#8377;{{ number_format($displayPrice) }}</h2><small>{{ $selectedCity?->name }} starting price</small></div><a class="whatsapp-btn" href="https://wa.me/{{ \App\Models\Setting::getValue('whatsapp_number') }}?text={{ urlencode('Hi, I want to enquire about '.$service->title) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a></div>
                        <form class="detail-cart-form" method="post" action="{{ route('cart.add') }}">
                            @csrf<input type="hidden" name="service_id" value="{{ $service->id }}"><input type="hidden" name="city_id" value="{{ $selectedCity?->id }}">
                            @if($service->addons->isNotEmpty())
                                <label class="form-label">Choose add-ons</label>
                                <div class="detail-addon-list">
                                    @foreach($service->addons as $addon)
                                        <label class="detail-addon-card">
                                            <input type="checkbox" name="addon_ids[]" value="{{ $addon->id }}">
                                            <img src="{{ $addon->image_url }}" alt="{{ $addon->name }}" loading="lazy">
                                            <span><strong>{{ $addon->name }}</strong><small>+&#8377;{{ number_format((float) ($addon->pivot->price_override ?: $addon->price)) }}</small></span>
                                            <i class="fa-solid fa-check"></i>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <button class="btn btn-outline-party w-100 mt-3" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to party cart</button>
                        </form>
                        @auth<form method="post" action="{{ route('wishlist.store', $service) }}" class="mt-2">@csrf<button class="btn btn-light border w-100"><i class="fa-regular fa-heart"></i> Save to wishlist</button></form>@endauth
                        <div class="booking-divider"></div>
                        @include('partials.booking-form', ['service' => $service])
                    </div>
                    <div class="sidebar-trust"><span><i class="fa-solid fa-shield-halved"></i> Secure Razorpay payment</span><span><i class="fa-solid fa-headset"></i> Human booking support</span><span><i class="fa-solid fa-receipt"></i> Transparent city pricing</span></div>
                </aside>
            </div>
        </div>
    </div>
</section>

@if($service->reviews->isNotEmpty())<section class="section section-soft"><div class="container"><div class="section-heading"><span>Verified reviews</span><h2>Parents on this experience</h2></div><div class="row g-4">@foreach($service->reviews as $review)<div class="col-md-4"><article class="testimonial-card"><div class="stars">@for($i=0;$i<$review->rating;$i++)<i class="fa-solid fa-star"></i>@endfor</div><p>{{ $review->comment }}</p><strong>{{ $review->customer_name }}</strong></article></div>@endforeach</div></div></section>@endif
@if($relatedServices->isNotEmpty())<section class="section"><div class="container"><div class="section-heading with-action"><div><span>Related services</span><h2>More ideas for the party</h2></div><a class="arrow-link" href="{{ route('categories.show', $service->category) }}">View category <i class="fa-solid fa-arrow-right"></i></a></div><div class="row g-3 g-md-4">@foreach($relatedServices as $related)<div class="col-6 col-xl-3">@include('partials.service-card', ['service' => $related])</div>@endforeach</div></div></section>@endif
<div class="mobile-booking-bar"><div><small>From</small><strong>&#8377;{{ number_format($displayPrice) }}</strong></div><a class="btn btn-party" href="{{ route('booking.create', ['service' => $service->slug]) }}">Book now</a></div>
@endsection

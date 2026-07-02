@extends('layouts.app')

@section('content')
<section class="hero-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
                @php($hasBannerCopy = filled($banner->title) || filled($banner->subtitle) || filled($banner->button_text))
                <div class="swiper-slide hero-slide {{ $hasBannerCopy ? 'has-copy' : 'image-only' }}">
                    <img class="hero-banner-image" src="{{ $banner->image_url }}" alt="{{ $banner->title ?: 'Kids Party Planner banner' }}">
                    @if($hasBannerCopy)
                        <div class="container">
                            <div class="hero-copy d-none">
                                <span class="hero-kicker"><i class="fa-solid fa-location-dot"></i> {{ $selectedCity?->name ?? 'Delhi NCR' }} party booking</span>
                                @if(filled($banner->title))
                                    @if($loop->first)
                                        <h1>Kids Party Planner</h1>
                                        <strong class="hero-offer">{{ $banner->title }}</strong>
                                    @else
                                        <h2>{{ $banner->title }}</h2>
                                    @endif
                                @endif
                                @if(filled($banner->subtitle))
                                    <p>{{ $banner->subtitle }}</p>
                                @endif
                                @if(filled($banner->button_text))
                                    <div class="hero-actions">
                                        <a class="btn btn-party btn-lg" href="{{ url($banner->button_url ?: '/services') }}">{{ $banner->button_text }} <i class="fa-solid fa-arrow-right"></i></a>
                                        <a class="text-action" href="{{ route('booking.track') }}">Track a booking <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="search-band">
    <div class="container">
        <form action="{{ route('services.index') }}" class="search-box">
            <input type="hidden" name="city" value="{{ $selectedCity?->slug }}">
            <div class="search-field search-main"><i class="fa-solid fa-magnifying-glass"></i><div><label>What should we bring to the party?</label><input type="search" name="search" placeholder="Try magic, slime or face painting"></div></div>
            <div class="search-field"><i class="fa-solid fa-indian-rupee-sign"></i><div><label>Your budget</label><select name="max_price"><option value="">Any budget</option><option value="2000">Under &#8377;2,000</option><option value="3000">Under &#8377;3,000</option><option value="5000">Under &#8377;5,000</option></select></div></div>
            <button class="btn btn-party" type="submit" aria-label="Search services"><i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <div class="city-quick-links"><span>Popular:</span><a href="{{ route('services.index') }}?search=Magic">Magic show</a><a href="{{ route('services.index') }}?search=Games">Party games</a><a href="{{ route('services.index') }}?search=Face+Painting">Face painting</a><a href="{{ route('services.index') }}?search=Slime">Slime workshop</a></div>
        <div class="home-city-selector"><span>Choose your city</span>@foreach($cities as $city)<form method="post" action="{{ route('city.select') }}">@csrf<input type="hidden" name="city_id" value="{{ $city->id }}"><button class="{{ $selectedCity?->id === $city->id ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i> {{ $city->name }}</button></form>@endforeach</div>
    </div>
</section>

<section class="section category-section">
    <div class="container">
        <div class="section-heading with-action"><div><span>Find their kind of fun</span><h2>What are we celebrating with?</h2></div><a href="{{ route('services.index') }}" class="arrow-link">See everything <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="category-grid">
            @foreach($categories as $category)
                <a class="category-tile" href="{{ route('categories.show', $category) }}">
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
                    <div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $category->name }}</h3><p>{{ $category->services_count }} ways to celebrate</p></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-heading with-action"><div><span>Parent favourites</span><h2>Booked again and again</h2></div><a href="{{ route('services.index') }}" class="arrow-link">Browse all services <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="row g-4">
            @foreach($featuredServices as $service)
                <div class="col-6 col-xl-3">@include('partials.service-card', ['service' => $service])</div>
            @endforeach
        </div>
    </div>
</section>

@foreach([
    'kids-activities-games' => ['Kids activities & games', 'Bring the energy, laughter and hands-on fun.'],
    'birthday-decoration' => ['Birthday decoration', 'From simple balloons to complete character themes.'],
    'anniversary-decoration' => ['Anniversary decoration', 'Warm, romantic setups for meaningful milestones.'],
    'new-born-baby-decoration' => ['New born baby decoration', 'Welcome-home and naming ceremony celebrations.'],
] as $slug => [$title, $copy])
    @php($shelf = $categoryServices->get($slug))
    @if($shelf && $shelf->services->isNotEmpty())
        <section class="section category-shelf {{ $loop->even ? 'section-soft' : '' }}"><div class="container"><div class="section-heading with-action"><div><span>Explore by celebration</span><h2>{{ $title }}</h2><p class="section-lead">{{ $copy }}</p></div><a class="arrow-link" href="{{ route('categories.show',$shelf) }}">View category <i class="fa-solid fa-arrow-right"></i></a></div><div class="row g-3 g-md-4">@foreach($shelf->services as $service)<div class="col-6 col-xl-3">@include('partials.service-card',['service'=>$service])</div>@endforeach</div></div></section>
    @endif
@endforeach

<section class="section package-showcase">
    <div class="container">
        <div class="row g-5 align-items-end mb-4"><div class="col-lg-7"><div class="section-heading text-start mb-0"><span>Ready-made party plans</span><h2>Good decisions, already bundled.</h2></div></div><div class="col-lg-5"><p class="section-lead">Pick a package when you want the entertainment, activities and coordination sorted together.</p></div></div>
        <div class="row g-4">
            @foreach($packages as $package)
                <div class="col-6 col-xl-3">@include('partials.package-card', ['package' => $package])</div>
            @endforeach
        </div>
    </div>
</section>

<section class="section process-section">
    <div class="container">
        <div class="section-heading"><span>Four calm steps</span><h2>From idea to party day</h2></div>
        <div class="process-grid">
            @foreach([
                ['01', 'fa-magnifying-glass', 'Discover', 'Compare activities, inclusions and local prices.'],
                ['02', 'fa-location-dot', 'Add details', 'Tell us the city, venue, date and kids count.'],
                ['03', 'fa-credit-card', 'Reserve', 'Pay the city-specific advance or the full amount.'],
                ['04', 'fa-face-smile', 'Celebrate', 'Track confirmation while our team gets party-ready.'],
            ] as $step)
                <div class="process-card"><span>{{ $step[0] }}</span><i class="fa-solid {{ $step[1] }}"></i><h3>{{ $step[2] }}</h3><p>{{ $step[3] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="trust-band">
    <div class="container">
        <div class="trust-copy"><span class="mini-label">Made for real birthday logistics</span><h2>Happy kids. Unhurried parents.</h2><p>Clear inclusions, verified artists and one dashboard from payment to completion.</p><a class="btn btn-dark" href="{{ route('about') }}">Why parents choose us</a></div>
        <div class="trust-photo"><img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=86" alt="Children enjoying a birthday celebration" loading="lazy"><div class="trust-stat"><strong>4.8/5</strong><span>Average parent rating</span></div></div>
        <div class="trust-points"><div><i class="fa-solid fa-user-check"></i><strong>Trained artists</strong><span>Child-friendly entertainers and coordinators.</span></div><div><i class="fa-solid fa-receipt"></i><strong>Clear pricing</strong><span>See city fees and advance before checkout.</span></div><div><i class="fa-solid fa-headset"></i><strong>Human support</strong><span>Real coordinators before and during the event.</span></div></div>
    </div>
</section>

<section class="section gallery-section">
    <div class="container">
        <div class="section-heading with-action"><div><span>Party scenes</span><h2>A little proof of fun</h2></div><a href="{{ route('gallery') }}" class="arrow-link">Open gallery <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="gallery-grid home-gallery">@foreach($gallery as $item)<figure><img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy"><figcaption>{{ $item->title }}<span>{{ $item->type }}</span></figcaption></figure>@endforeach</div>
    </div>
</section>

<section class="section testimonial-section">
    <div class="container">
        <div class="section-heading"><span>Notes from parents</span><h2>The best feedback is “again!”</h2></div>
        <div class="swiper testimonial-swiper"><div class="swiper-wrapper">@foreach($reviews as $review)<div class="swiper-slide"><article class="testimonial-card"><div class="quote-mark">&ldquo;</div><div class="stars">@for($i = 0; $i < $review->rating; $i++)<i class="fa-solid fa-star"></i>@endfor</div><p>{{ $review->comment }}</p><div class="review-author"><span>{{ strtoupper(substr($review->customer_name, 0, 1)) }}</span><div><strong>{{ $review->customer_name }}</strong><small>Verified parent</small></div></div></article></div>@endforeach</div></div>
    </div>
</section>

<section class="social-cta"><div class="container"><div><span class="mini-label"><i class="fa-brands fa-instagram"></i> @kidspartyplanner1</span><h2>Fresh setups, real parties, new ideas.</h2><p>Follow our latest Delhi NCR celebrations and theme inspiration.</p></div><a class="btn btn-light" href="{{ \App\Models\Setting::getValue('instagram_url','https://www.instagram.com/kidspartyplanner1/') }}" target="_blank" rel="noopener">Follow on Instagram <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div></section>

<section class="section faq-section">
    <div class="container"><div class="row g-5"><div class="col-lg-4"><div class="section-heading text-start"><span>Questions, answered</span><h2>Before you book</h2></div><p class="section-lead">Need something unusual? Our WhatsApp team can help shape a custom plan.</p></div><div class="col-lg-8"><div class="accordion" id="homeFaq">@foreach($faqs as $faq)<div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button @if(!$loop->first) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $loop->index }}">{{ $faq['question'] }}</button></h3><div id="faq{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif" data-bs-parent="#homeFaq"><div class="accordion-body">{{ $faq['answer'] }}</div></div></div>@endforeach</div></div></div></div>
</section>
@endsection

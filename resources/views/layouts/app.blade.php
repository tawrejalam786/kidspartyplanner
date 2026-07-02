<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? \App\Models\Setting::getValue('meta_title', config('app.name')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? \App\Models\Setting::getValue('meta_description', 'Kids birthday party booking in Delhi NCR.') }}">
    @if($metaKeywords ?? null)<meta name="keywords" content="{{ $metaKeywords }}">@endif
    <meta property="og:title" content="{{ $metaTitle ?? \App\Models\Setting::getValue('meta_title', config('app.name')) }}">
    <meta property="og:description" content="{{ $metaDescription ?? \App\Models\Setting::getValue('meta_description', '') }}">
    @if($ogImage ?? null)<meta property="og:image" content="{{ $ogImage }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>
<body>
    <header class="mobile-app-header">
        <div class="mobile-service-strip">
            <span><i class="fa-solid fa-bolt"></i> Earliest <strong>tomorrow</strong></span>
            <span><i class="fa-regular fa-calendar"></i> Book up to <strong>90 days</strong></span>
        </div>
        <div class="mobile-location-row">
            <a class="mobile-brand" href="{{ route('home') }}" aria-label="Kids Party Planner home"><span class="brand-logo-frame mobile-logo-frame"><img src="{{ asset('assets/images/kidspartyplanner-logo.png') }}" alt="Kids Party Planner"></span></a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#cityModal"><i class="fa-solid fa-location-dot"></i><span>{{ $selectedCity?->name ?? 'Select city' }}</span><i class="fa-solid fa-chevron-down"></i></button>
        </div>
        <div class="mobile-search-row">
            <form action="{{ route('services.index') }}">
                <input type="hidden" name="city" value="{{ $selectedCity?->slug }}">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" name="search" placeholder="Search activities and decoration">
                <button aria-label="Search"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
            @auth
                <a class="mobile-account-button" href="{{ route(auth()->user()->dashboardRouteName()) }}" aria-label="Dashboard">@if(auth()->user()->avatar_url)<img src="{{ auth()->user()->avatar_url }}" alt="">@else<i class="fa-regular fa-user"></i>@endif</a>
            @else
                <a class="mobile-login-button" href="{{ route('login') }}"><i class="fa-regular fa-user"></i> Login</a>
            @endauth
        </div>
        <nav class="mobile-category-nav" aria-label="Party categories">
            <a class="{{ request()->routeIs('categories.index') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="fa-solid fa-border-all"></i> All</a>
            @foreach($navCategories as $category)
                <a class="{{ request()->routeIs('categories.show') && request()->route('category') instanceof \App\Models\Category && request()->route('category')->is($category) ? 'active' : '' }}" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
            @endforeach
        </nav>
    </header>

    <header class="site-header desktop-site-header sticky-top">
        <div class="utility-bar">
            <div class="container">
                <span><i class="fa-solid fa-location-dot"></i> Serving Delhi, Noida & Gurgaon</span>
                <div><a href="mailto:{{ \App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in') }}"><i class="fa-solid fa-envelope"></i> {{ \App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in') }}</a><a href="{{ \App\Models\Setting::getValue('instagram_url', 'https://www.instagram.com/kidspartyplanner1/') }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i> Instagram</a><a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::getValue('site_phone', '+91 9910434330')) }}"><i class="fa-solid fa-phone"></i> {{ \App\Models\Setting::getValue('site_phone', '+91 9910434330') }}</a></div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg p-0">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}" aria-label="Kids Party Planner home">
                    <span class="brand-logo-frame desktop-logo-frame"><img src="{{ asset('assets/images/kidspartyplanner-logo.png') }}" alt="Kids Party Planner"></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto align-items-lg-center">
                        <li class="nav-item dropdown categories-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('categories.*','subcategories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}" data-bs-toggle="dropdown" data-bs-auto-close="outside">Discover Celebrations</a>
                            <div class="dropdown-menu category-mega-menu">
                                <div class="category-menu-head"><span>Explore celebrations</span><a href="{{ route('categories.index') }}">View all <i class="fa-solid fa-arrow-right"></i></a></div>
                                <div class="category-menu-grid">
                                    @foreach($navCategories as $category)
                                        <div class="category-menu-item">
                                            <a class="category-menu-title" href="{{ route('categories.show', $category) }}"><img src="{{ $category->image_url }}" alt=""><span><strong>{{ $category->name }}</strong><small>{{ $category->description }}</small></span></a>
                                            @foreach($category->subcategories->take(3) as $subcategory)<a href="{{ route('subcategories.show', $subcategory) }}">{{ $subcategory->name }}</a>@endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('services.*', 'categories.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Services</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">Packages</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('reviews') ? 'active' : '' }}" href="{{ route('reviews') }}">Reviews</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('booking.track') ? 'active' : '' }}" href="{{ route('booking.track') }}">Track</a></li>
                    </ul>
                    <div class="nav-actions">
                        <button class="city-trigger" type="button" data-bs-toggle="modal" data-bs-target="#cityModal"><i class="fa-solid fa-location-dot"></i><span>{{ $selectedCity?->name ?? 'Select city' }}</span><i class="fa-solid fa-chevron-down"></i></button>
                        <a class="icon-link cart-link" href="{{ route('cart.index') }}" title="Party cart" aria-label="Party cart"><i class="fa-solid fa-bag-shopping"></i>@if($cartCount)<span>{{ $cartCount }}</span>@endif</a>
                        @auth
                            <a class="icon-link" href="{{ route(auth()->user()->dashboardRouteName()) }}" title="Dashboard" aria-label="Dashboard"><i class="fa-regular fa-user"></i></a>
                            <form action="{{ route('logout') }}" method="post">@csrf<button class="icon-link" title="Logout" aria-label="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
                        @else
                            <a class="login-link" href="{{ route('login') }}"><i class="fa-regular fa-user"></i> Login</a>
                        @endauth
                        <a class="btn btn-party btn-sm" href="{{ route('booking.create') }}">Book a party <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    @if(session('success') || $errors->any())
        <div class="container flash-wrap">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ $errors->first() }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-intro">
                <div><span class="mini-label">Party help, minus the panic</span><h2>Make the next birthday the easy one.</h2></div>
                <a class="btn btn-light" href="{{ route('booking.create') }}">Start booking <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="row g-4 footer-links">
                <div class="col-lg-4">
                    <a class="footer-brand" href="{{ route('home') }}"><span class="brand-logo-frame footer-logo-frame"><img src="{{ asset('assets/images/kidspartyplanner-logo.png') }}" alt="Kids Party Planner"></span></a>
                    <p>Entertainers, activity artists and ready party packages for joyful birthdays across Delhi NCR.</p>
                    <p class="footer-address"><i class="fa-solid fa-location-dot"></i> {{ \App\Models\Setting::getValue('site_address', 'TC-37, Pandav Nagar, Shadipur, New Delhi - 110008') }}</p>
                </div>
                <div class="col-6 col-lg-2"><h6>Discover</h6><a href="{{ route('categories.index') }}">Categories</a><a href="{{ route('services.index') }}">Services</a><a href="{{ route('packages.index') }}">Packages</a><a href="{{ route('gallery') }}">Gallery</a></div>
                <div class="col-6 col-lg-2"><h6>Company</h6><a href="{{ route('about') }}">About</a><a href="{{ route('reviews') }}">Reviews</a><a href="{{ route('faq') }}">FAQ</a><a href="{{ route('contact') }}">Contact</a></div>
                <div class="col-6 col-lg-2"><h6>Legal</h6><a href="{{ route('terms') }}">Terms</a><a href="{{ route('privacy') }}">Privacy</a><a href="{{ route('refund') }}">Refunds</a></div>
                <div class="col-6 col-lg-2"><h6>Talk to us</h6><a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::getValue('site_phone', '+91 9910434330')) }}">Call us</a><a href="mailto:{{ \App\Models\Setting::getValue('site_email', 'sales@kidspartyplanner.in') }}">Email</a><a href="{{ \App\Models\Setting::getValue('instagram_url', '#') }}" target="_blank">Instagram</a><a href="{{ route('booking.track') }}">Track booking</a></div>
            </div>
            <div class="footer-bottom"><span>&copy; {{ date('Y') }} Kids Party Planner</span><span>Made for happier birthdays in Delhi NCR</span></div>
        </div>
    </footer>

    @if($recentBookingActivity->isNotEmpty())
        <div class="booking-activity-feed" aria-live="polite">
            @foreach($recentBookingActivity as $activity)
                <div class="booking-activity-item" data-booking-activity>
                    <span><i class="fa-solid fa-check"></i></span>
                    <div><strong>{{ \Illuminate\Support\Str::before($activity->customer_name, ' ') }} booked {{ $activity->item_title }}</strong><small>{{ $activity->city?->name ?? $activity->cityPaymentSetting?->city ?? 'Delhi NCR' }} &middot; {{ $activity->updated_at->diffForHumans() }}</small></div>
                </div>
            @endforeach
        </div>
    @endif

    <a class="whatsapp-float" href="https://wa.me/{{ \App\Models\Setting::getValue('whatsapp_number', config('services.whatsapp.number')) }}?text={{ urlencode('Hi Kids Party Planner, I want to enquire about a birthday party booking.') }}" target="_blank" rel="noopener" aria-label="WhatsApp enquiry"><i class="fa-brands fa-whatsapp"></i><span>Enquire</span></a>

    <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
        <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a class="{{ request()->routeIs('categories.*','subcategories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="fa-solid fa-shapes"></i><span>Categories</span></a>
        <a class="mobile-book-action" href="{{ route('booking.create') }}"><i class="fa-solid fa-calendar-plus"></i><span>Book</span></a>
        <a class="{{ request()->routeIs('cart.*','checkout.*') ? 'active' : '' }}" href="{{ route('cart.index') }}"><i class="fa-solid fa-cart-shopping"></i>@if($cartCount)<b>{{ $cartCount }}</b>@endif<span>Cart</span></a>
        <a class="{{ request()->routeIs('dashboard*','vendor.*','login') ? 'active' : '' }}" href="{{ auth()->check() ? route(auth()->user()->dashboardRouteName()) : route('login') }}"><i class="fa-regular fa-user"></i><span>{{ auth()->check() ? 'Account' : 'Login' }}</span></a>
    </nav>

    <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content city-modal"><div class="modal-header"><div><span class="mini-label">Service location</span><h2 id="cityModalLabel">Where is the party?</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="city-options">@foreach($activeCities as $city)<form method="post" action="{{ route('city.select') }}">@csrf<input type="hidden" name="city_id" value="{{ $city->id }}"><button class="city-option {{ $selectedCity?->id === $city->id ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i><span><strong>{{ $city->name }}</strong><small>{{ $city->state }}</small></span>@if($selectedCity?->id === $city->id)<i class="fa-solid fa-check"></i>@endif</button></form>@endforeach</div>@if($futureCities->isNotEmpty())<span class="mini-label mt-4">Coming soon</span><div class="future-cities">@foreach($futureCities as $city)<span>{{ $city->name }}</span>@endforeach</div>@endif</div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>

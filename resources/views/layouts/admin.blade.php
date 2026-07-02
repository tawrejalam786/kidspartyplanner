<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} | Kids Party Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="admin-body">
    @php
        $resources = [
            'bookings' => ['Bookings', 'fa-ticket'],
            'payments' => ['Payments', 'fa-credit-card'],
            'refunds' => ['Refunds', 'fa-rotate-left'],
            'cities' => ['Cities', 'fa-city'],
            'areas' => ['Areas', 'fa-map-location-dot'],
            'city-payments' => ['City Payments', 'fa-building-columns'],
            'services' => ['Services', 'fa-wand-magic-sparkles'],
            'service-images' => ['Service Images', 'fa-images'],
            'service-prices' => ['Service Pricing', 'fa-indian-rupee-sign'],
            'addons' => ['Add-ons', 'fa-puzzle-piece'],
            'packages' => ['Packages', 'fa-box-open'],
            'categories' => ['Categories', 'fa-shapes'],
            'subcategories' => ['Subcategories', 'fa-layer-group'],
            'customers' => ['Customers', 'fa-users'],
            'admins' => ['Admin Users', 'fa-user-shield'],
            'vendors' => ['Vendors', 'fa-handshake'],
            'booking-assignments' => ['Assignments', 'fa-clipboard-check'],
            'vendor-earnings' => ['Vendor Earnings', 'fa-wallet'],
            'vendor-withdrawals' => ['Withdrawals', 'fa-money-bill-transfer'],
            'enquiries' => ['Enquiries', 'fa-inbox'],
            'reviews' => ['Reviews', 'fa-star'],
            'galleries' => ['Gallery', 'fa-images'],
            'banners' => ['Banners', 'fa-panorama'],
            'faqs' => ['FAQs', 'fa-circle-question'],
            'coupons' => ['Coupons', 'fa-tags'],
            'blogs' => ['Blogs', 'fa-pen-nib'],
            'pages' => ['CMS Pages', 'fa-file-lines'],
            'settings' => ['Settings', 'fa-gear'],
        ];
    @endphp
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-head">
                <a class="admin-brand" href="{{ route('admin.dashboard') }}"><span class="brand-logo-frame admin-logo-frame"><img src="{{ asset('assets/images/kids-party-planner-logo.jpg') }}" alt="Kids Party Planner"></span><span>Admin</span></a>
                <button class="admin-menu-toggle" type="button" aria-label="Toggle admin navigation"><i class="fa-solid fa-bars"></i></button>
            </div>
            <nav>
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                @foreach($resources as $slug => [$label, $icon])
                    <a class="{{ request()->is('admin/'.$slug.'*') ? 'active' : '' }}" href="{{ route('admin.resources.index', $slug) }}">
                        <i class="fa-solid {{ $icon }}"></i> {{ $label }}
                    </a>
                @endforeach
            </nav>
            <form action="{{ route('logout') }}" method="post">@csrf<button class="btn btn-outline-light w-100 mt-3">Logout</button></form>
        </aside>
        <div class="admin-main">
            <div class="admin-topbar">
                <div>
                    <span>Admin Panel</span>
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-party btn-sm">View Site</a>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>document.querySelector('.admin-menu-toggle')?.addEventListener('click', function () { document.querySelector('.admin-sidebar')?.classList.toggle('sidebar-open'); });</script>
</body>
</html>

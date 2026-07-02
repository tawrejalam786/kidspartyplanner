@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $icons = ['Bookings' => 'fa-ticket', 'Today Bookings' => 'fa-calendar-day', 'Upcoming Bookings' => 'fa-calendar-check', 'Confirmed Bookings' => 'fa-circle-check', 'Cancelled Bookings' => 'fa-ban', 'Pending Payments' => 'fa-clock', 'Customers' => 'fa-users', 'Vendors' => 'fa-handshake', 'Total Revenue' => 'fa-indian-rupee-sign'];
    $statusTotal = max(1, $statusCounts->sum());
    $maxCityBookings = max(1, (int) $cityStats->max('bookings_count'));
@endphp
<div class="admin-stats">
    @foreach($stats as $label => $value)
        <div class="admin-stat"><span><i class="fa-solid {{ $icons[$label] ?? 'fa-chart-simple' }} me-2"></i>{{ $label }}</span><strong>@if($label === 'Total Revenue')&#8377;@endif{{ number_format((float) $value) }}</strong></div>
    @endforeach
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="admin-card h-100 event-focus-card">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Call today</span><h2 class="mb-0">Today's events</h2></div><a href="{{ route('admin.resources.index', 'bookings', ['search' => today()->format('Y-m-d')]) }}" class="icon-link"><i class="fa-solid fa-arrow-right"></i></a></div>
            @forelse($todayEvents as $booking)
                <div class="admin-mini event-focus-row"><strong>{{ $booking->item_title }}</strong><span>{{ \Illuminate\Support\Str::of($booking->event_time)->substr(0,5) }} &middot; {{ $booking->customer_name }} &middot; {{ $booking->customer_phone }}</span><small>{{ $booking->full_address ?: $booking->location }}</small></div>
            @empty
                <div class="empty-state py-4">No events scheduled for today.</div>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card h-100 event-focus-card">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Next 7 days</span><h2 class="mb-0">Upcoming follow-ups</h2></div><a href="{{ route('admin.resources.index', 'bookings') }}" class="btn btn-outline-party btn-sm">All bookings</a></div>
            @forelse($upcomingEvents as $booking)
                <div class="admin-mini event-focus-row"><strong>{{ $booking->event_date->format('d M') }} &middot; {{ $booking->item_title }}</strong><span>{{ $booking->customer_name }} &middot; {{ $booking->customer_phone }} &middot; {{ $booking->city?->name ?? $booking->cityPaymentSetting?->city ?? '-' }}</span></div>
            @empty
                <div class="empty-state py-4">No upcoming follow-ups this week.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-8">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Live operations</span><h2 class="mb-0">Recent bookings</h2></div><a href="{{ route('admin.resources.index', 'bookings') }}" class="btn btn-outline-party btn-sm">View all</a></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Booking</th><th>Customer</th><th>City</th><th>Event</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>@forelse($bookings as $booking)<tr><td><strong>{{ $booking->item_title }}</strong><br><small class="text-muted">{{ $booking->booking_no }}</small></td><td>{{ $booking->customer_name }}</td><td>{{ $booking->city?->name ?? $booking->cityPaymentSetting?->city ?? '-' }}</td><td>{{ $booking->event_date->format('d M Y') }}</td><td><span class="status-badge">{{ $booking->workflow_status }}</span></td><td>&#8377;{{ number_format((float) $booking->total_amount) }}</td></tr>@empty<tr><td colspan="6" class="text-center py-4">No bookings yet.</td></tr>@endforelse</tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="admin-card h-100">
            <span class="mini-label mb-1">Booking pipeline</span><h2>Status overview</h2>
            <div class="admin-status-grid mt-3">@foreach($statusCounts as $status => $count)<div class="admin-status-item"><span>{{ $status }}</span><strong>{{ $count }}</strong><div class="progress mt-2" style="height:4px"><div class="progress-bar" style="width:{{ ($count / $statusTotal) * 100 }}%; background:var(--{{ $status === 'Cancelled' ? 'coral' : 'mint-dark' }})"></div></div></div>@endforeach</div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-7">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><span class="mini-label mb-1">Demand by location</span><h2 class="mb-0">City performance</h2></div><a href="{{ route('admin.resources.index', 'city-payments') }}" class="btn btn-outline-party btn-sm">Payment rules</a></div>
            <div class="city-performance">@foreach($cityStats as $city)<div class="city-performance-row"><strong>{{ $city->city }}</strong><div class="progress"><div class="progress-bar" style="width:{{ ($city->bookings_count / $maxCityBookings) * 100 }}%"></div></div><span>{{ $city->bookings_count }} bookings</span></div>@endforeach</div><hr><span class="mini-label mb-2">Top selling services</span>@foreach($topServices as $service)<div class="admin-mini"><strong>{{ $service->title }}</strong><span>{{ $service->bookings_count }} direct bookings</span></div>@endforeach
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-2"><div><span class="mini-label mb-1">Needs attention</span><h2 class="mb-0">New enquiries</h2></div><a href="{{ route('admin.resources.index', 'enquiries') }}" class="icon-link"><i class="fa-solid fa-arrow-right"></i></a></div>
            @forelse($enquiries as $enquiry)<div class="admin-mini"><strong>{{ $enquiry->name }}</strong><span>{{ $enquiry->phone }} &middot; {{ $enquiry->status }}</span></div>@empty<div class="empty-state py-4">No new enquiries.</div>@endforelse
        </div>
    </div>
</div>
@endsection

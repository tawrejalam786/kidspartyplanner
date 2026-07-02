@extends('layouts.app')

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-welcome">
            <div><span class="mini-label">Customer dashboard</span><h1>Hello, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.</h1><p class="section-lead">Your parties, payments and confirmations are all here.</p></div>
            <div class="dashboard-actions"><a class="icon-link" href="{{ route('dashboard.profile') }}" title="Profile"><i class="fa-regular fa-user"></i></a><a class="icon-link" href="{{ route('wishlist.index') }}" title="Wishlist"><i class="fa-regular fa-heart"></i></a><a class="icon-link" href="{{ route('dashboard.payments') }}" title="Payments"><i class="fa-regular fa-credit-card"></i></a><a class="btn btn-party" href="{{ route('services.index') }}"><i class="fa-solid fa-plus"></i> New booking</a></div>
        </div>
        @if($todayBookings->isNotEmpty())
            <div class="dashboard-alert">
                <i class="fa-solid fa-calendar-day"></i>
                <div><strong>Your party is scheduled today.</strong><span>{{ $todayBookings->pluck('item_title')->join(', ') }}. Our team will coordinate on your registered mobile number.</span></div>
            </div>
        @endif
        <div class="dashboard-stats">
            <div class="dashboard-stat"><i class="fa-solid fa-ticket"></i><span>Total bookings</span><strong>{{ $stats['total'] }}</strong></div>
            <div class="dashboard-stat"><i class="fa-regular fa-calendar"></i><span>Upcoming parties</span><strong>{{ $stats['upcoming'] }}</strong></div>
            <div class="dashboard-stat"><i class="fa-regular fa-credit-card"></i><span>Payments due</span><strong>{{ $stats['pending_payment'] }}</strong></div>
            <div class="dashboard-stat"><i class="fa-solid fa-indian-rupee-sign"></i><span>Paid securely</span><strong>&#8377;{{ number_format((float) $stats['paid']) }}</strong></div>
        </div>
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head"><h2>Recent bookings</h2><a class="arrow-link" href="{{ route('services.index') }}">Explore more <i class="fa-solid fa-arrow-right"></i></a></div>
                    <div class="table-responsive dashboard-table">
                        <table class="table align-middle">
                            <thead><tr><th>Experience</th><th>Event</th><th>City</th><th>Status</th><th>Payment</th><th></th></tr></thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td><div class="booking-item-cell"><span><i class="fa-solid fa-wand-magic-sparkles"></i></span><div><strong>{{ $booking->item_title }}</strong><small>{{ $booking->booking_no }}</small></div></div></td>
                                        <td>{{ $booking->event_date->format('d M Y') }}</td>
                                        <td>{{ $booking->city?->name ?? $booking->cityPaymentSetting?->city ?? '-' }}</td>
                                        <td><span class="status-badge">{{ $booking->workflow_status }}</span></td>
                                        <td>{{ $booking->payment_status }}</td>
                                        <td><a class="icon-link" href="{{ route('dashboard.booking', $booking) }}" aria-label="View {{ $booking->booking_no }}"><i class="fa-solid fa-arrow-right"></i></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5"><strong>No bookings yet.</strong><br><span class="text-muted">Your next celebration can start here.</span></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $bookings->links() }}</div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="dashboard-panel h-100">
                    <div class="dashboard-panel-head"><h2>Next party</h2><i class="fa-regular fa-calendar-check"></i></div>
                    @if($nextBooking)
                        <span class="mini-label">{{ $nextBooking->workflow_status }}</span>
                        <h3 class="mt-1">{{ $nextBooking->item_title }}</h3>
                        <div class="detail-grid mt-3"><div><span>Date</span><strong>{{ $nextBooking->event_date->format('d M') }}</strong></div><div><span>Time</span><strong>{{ \Illuminate\Support\Str::of($nextBooking->event_time)->substr(0, 5) }}</strong></div><div><span>City</span><strong>{{ $nextBooking->city?->name }}</strong></div><div><span>Kids</span><strong>{{ $nextBooking->number_of_kids }}</strong></div></div>
                        <a class="btn btn-outline-party w-100 mt-3" href="{{ route('dashboard.booking', $nextBooking) }}">Open booking</a>
                    @else
                        <div class="empty-state"><i class="fa-regular fa-calendar-plus fa-2x mb-3"></i><p>No upcoming party yet.</p><a href="{{ route('services.index') }}" class="arrow-link">Find an activity <i class="fa-solid fa-arrow-right"></i></a></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<section class="dashboard-shell vendor-dashboard-shell">
    <div class="container">
        <div class="dashboard-welcome">
            <div><span class="mini-label">Vendor dashboard</span><h1>{{ $vendor->business_name }}</h1><p class="section-lead">{{ $vendor->city ?: 'Service city' }} &middot; {{ $vendor->status }}</p></div>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-party">Logout</button></form>
        </div>

        @if($vendor->status !== 'Approved')
            <div class="dashboard-alert">
                <i class="fa-solid fa-hourglass-half"></i>
                <div><strong>Approval pending</strong><span>Admin needs to approve this vendor profile before live job assignment starts.</span></div>
            </div>
        @endif

        <div class="dashboard-stats">
            <div class="dashboard-stat"><i class="fa-solid fa-clipboard-check"></i><span>Total jobs</span><strong>{{ $stats['assigned'] }}</strong></div>
            <div class="dashboard-stat"><i class="fa-solid fa-person-running"></i><span>Active jobs</span><strong>{{ $stats['active'] }}</strong></div>
            <div class="dashboard-stat"><i class="fa-solid fa-wallet"></i><span>Available</span><strong>&#8377;{{ number_format((float) $stats['available']) }}</strong></div>
            <div class="dashboard-stat"><i class="fa-regular fa-clock"></i><span>Pending</span><strong>&#8377;{{ number_format((float) $stats['pending']) }}</strong></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head"><h2>Assigned jobs</h2><span class="status-badge">{{ $vendor->status }}</span></div>
                    <div class="table-responsive dashboard-table">
                        <table class="table align-middle">
                            <thead><tr><th>Booking</th><th>Event</th><th>Customer</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td><strong>{{ $assignment->booking->item_title }}</strong><br><small>{{ $assignment->booking->booking_no }} &middot; {{ $assignment->booking->city?->name ?? $assignment->booking->cityPaymentSetting?->city }}</small></td>
                                        <td>{{ $assignment->booking->event_date->format('d M Y') }}<br><small>{{ \Illuminate\Support\Str::of($assignment->booking->event_time)->substr(0, 5) }}</small></td>
                                        <td>{{ $assignment->booking->customer_name }}<br><small>{{ $assignment->booking->customer_phone }}</small></td>
                                        <td>&#8377;{{ number_format((float) $assignment->vendor_earning) }}</td>
                                        <td><span class="status-badge">{{ $assignment->status }}</span></td>
                                        <td>
                                            <div class="vendor-job-actions">
                                                @if($assignment->status === 'Assigned')
                                                    <form method="post" action="{{ route('vendor.assignments.accept', $assignment) }}">@csrf<button class="btn btn-outline-party btn-sm">Accept</button></form>
                                                @endif
                                                @if(in_array($assignment->status, ['Assigned', 'Accepted', 'In Progress'], true))
                                                    <form method="post" action="{{ route('vendor.assignments.complete', $assignment) }}">@csrf<button class="btn btn-party btn-sm">Complete</button></form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5">No jobs assigned yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $assignments->links() }}</div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="dashboard-panel mb-4">
                    <div class="dashboard-panel-head"><h2>Withdraw earnings</h2><i class="fa-solid fa-money-bill-transfer"></i></div>
                    <form method="post" action="{{ route('vendor.withdrawals.store') }}">
                        @csrf
                        <label class="form-label">Amount</label>
                        <input class="form-control" type="number" name="amount" min="100" max="{{ (float) $stats['available'] }}" value="{{ old('amount') }}" required>
                        <button class="btn btn-party w-100 mt-3" @disabled((float) $stats['available'] < 100)>Request withdrawal</button>
                    </form>
                </div>
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head"><h2>Service coverage</h2><i class="fa-solid fa-list-check"></i></div>
                    <div class="vendor-service-list">
                        @foreach($vendor->services as $service)<span>{{ $service->title }}</span>@endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6"><div class="dashboard-panel"><div class="dashboard-panel-head"><h2>Recent earnings</h2></div>@forelse($earnings as $earning)<div class="admin-mini"><strong>&#8377;{{ number_format((float) $earning->net_amount) }} &middot; {{ $earning->status }}</strong><span>{{ $earning->booking->booking_no }} &middot; Commission &#8377;{{ number_format((float) $earning->commission_amount) }}</span></div>@empty<div class="empty-state py-4">No earnings yet.</div>@endforelse</div></div>
            <div class="col-lg-6"><div class="dashboard-panel"><div class="dashboard-panel-head"><h2>Withdrawal history</h2></div>@forelse($withdrawals as $withdrawal)<div class="admin-mini"><strong>&#8377;{{ number_format((float) $withdrawal->amount) }} &middot; {{ $withdrawal->status }}</strong><span>{{ $withdrawal->payout_reference ?: 'Awaiting payout reference' }}</span></div>@empty<div class="empty-state py-4">No withdrawals yet.</div>@endforelse</div></div>
        </div>
    </div>
</section>
@endsection

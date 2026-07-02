@extends('layouts.app')

@section('content')
<section class="page-hero booking-hero">
    <div class="container"><span class="eyebrow">Secure checkout</span><h1>One last step to reserve the fun.</h1><p>{{ $booking->booking_no }} &middot; {{ $booking->item_title }} &middot; {{ $booking->cityPaymentSetting?->city }}</p></div>
</section>
<section class="section booking-page">
    <div class="container">
        <div class="row g-4 justify-content-center align-items-start">
            <div class="col-lg-7">
                <div class="payment-checkout-card">
                    <div class="payment-checkout-head"><span class="mini-label">Amount due now</span><h2>&#8377;{{ number_format((float) $booking->payable_amount, 2) }}</h2><small>{{ ucfirst($booking->payment_type) }} payment for {{ $booking->cityPaymentSetting?->city ?? 'selected city' }}</small></div>
                    <div class="payment-breakdown">
                        <div><span>Service and add-ons</span><strong>&#8377;{{ number_format((float) $booking->base_amount, 2) }}</strong></div>
                        @if($booking->coupon_discount > 0)<div><span>Coupon {{ $booking->coupon_code }}</span><strong>-&#8377;{{ number_format((float) $booking->coupon_discount, 2) }}</strong></div>@endif
                        <div><span>{{ $booking->cityPaymentSetting?->city }} convenience fee</span><strong>&#8377;{{ number_format((float) $booking->service_fee, 2) }}</strong></div>
                        <div><span>Tax</span><strong>&#8377;{{ number_format((float) $booking->tax_amount, 2) }}</strong></div>
                        <div><strong>Total booking value</strong><strong>&#8377;{{ number_format((float) $booking->total_amount, 2) }}</strong></div>
                    </div>
                    <div class="payment-actions">
                        @if($razorpayConfigured)
                            <button class="btn btn-party btn-lg w-100" id="pay-razorpay" data-order-url="{{ route('payments.order', $booking) }}" data-verify-url="{{ route('payments.verify', $booking) }}" data-failed-url="{{ route('payments.failed', $booking) }}"><i class="fa-solid fa-lock"></i> Pay securely with Razorpay</button>
                        @else
                            <div class="alert alert-warning">Online payment is not configured for {{ $booking->cityPaymentSetting?->city ?? 'this city' }} yet. The booking is safe in your dashboard.</div>
                            <a class="btn btn-outline-party w-100" href="{{ route('dashboard.booking', $booking) }}">View booking</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <aside class="booking-assurance"><span class="mini-label">City payment note</span><h2>{{ $booking->cityPaymentSetting?->city ?? 'Delhi NCR' }}</h2><p>{{ $booking->cityPaymentSetting?->payment_instructions ?? 'Your booking is confirmed after payment verification.' }}</p><div class="assurance-item"><i class="fa-solid fa-lock"></i><div><strong>Verified checkout</strong><span>Payment signature is checked before confirmation.</span></div></div><div class="assurance-item"><i class="fa-solid fa-receipt"></i><div><strong>Saved automatically</strong><span>Transaction details appear in your dashboard.</span></div></div></aside>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endpush

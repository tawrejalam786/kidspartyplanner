@extends('layouts.app')

@section('content')
<section class="page-hero booking-hero">
    <div class="container">
        <span class="eyebrow">Easy booking</span>
        <h1>Your party, neatly planned.</h1>
        <p>Choose the city and see the exact local payment details before you confirm.</p>
    </div>
</section>
<section class="section booking-page">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-xl-8">
                <div class="booking-panel">
                    @if($service)
                        <span class="badge soft-badge mb-2">Service selected</span>
                        <h2>{{ $service->title }}</h2>
                    @elseif($package)
                        <span class="badge soft-badge mb-2">Package selected</span>
                        <h2>{{ $package->title }}</h2>
                    @else
                        <h2>Custom booking request</h2>
                    @endif
                    @include('partials.booking-form', compact('service', 'package', 'services', 'packages', 'cities'))
                </div>
            </div>
            <div class="col-xl-4">
                <aside class="booking-assurance">
                    <span class="mini-label">What happens next</span>
                    <h2>We hold the slot while you pay</h2>
                    <div class="assurance-item"><i class="fa-solid fa-phone-volume"></i><div><strong>Confirmation call</strong><span>Event details are verified by our coordinator.</span></div></div>
                    <div class="assurance-item"><i class="fa-solid fa-shield-heart"></i><div><strong>Secure payment</strong><span>City-specific Razorpay configuration and verification.</span></div></div>
                    <div class="assurance-item"><i class="fa-solid fa-calendar-check"></i><div><strong>Dashboard tracking</strong><span>Booking, status and payment history in one place.</span></div></div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection

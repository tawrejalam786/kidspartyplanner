@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Contact</span>
        <h1>Tell us about the party</h1>
        <p>Share date, location, age group, and activities you are considering.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-panel">
                    <h2>Kids Party Planner</h2>
                    <p><i class="fa-solid fa-phone"></i> {{ \App\Models\Setting::getValue('site_phone', '+91 99999 99999') }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ \App\Models\Setting::getValue('site_email', 'hello@kidspartyplanner.test') }}</p>
                    <p><i class="fa-solid fa-location-dot"></i> {{ \App\Models\Setting::getValue('service_area', 'Delhi NCR') }}</p>
                    <a class="btn btn-success" target="_blank" rel="noopener" href="https://wa.me/{{ \App\Models\Setting::getValue('whatsapp_number', config('services.whatsapp.number')) }}?text={{ urlencode('Hi Kids Party Planner, I need help planning a birthday party.') }}"><i class="fa-brands fa-whatsapp"></i> WhatsApp Enquiry</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="booking-panel">
                    <h2>Send enquiry</h2>
                    <form action="{{ route('enquiries.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="source" value="Contact Page">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email"></div>
                            <div class="col-md-6"><label class="form-label">Service</label><select class="form-select" name="service_id"><option value="">Not sure yet</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->title }}</option>@endforeach</select></div>
                            <div class="col-12"><label class="form-label">Subject</label><input class="form-control" name="subject" placeholder="Birthday party enquiry"></div>
                            <div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" required></textarea></div>
                        </div>
                        <button class="btn btn-party mt-4">Submit Enquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

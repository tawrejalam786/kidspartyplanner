@extends('layouts.app')

@section('content')
<section class="auth-section vendor-register-section">
    <div class="auth-card vendor-register-card">
        <span class="eyebrow">Vendor network</span>
        <h1>Join Kids Party Planner</h1>
        <p class="section-lead mb-4">Register your local party service team. Admin will verify your profile before assigning bookings.</p>
        <form method="post" action="{{ route('vendors.register.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Business name</label><input class="form-control" name="business_name" value="{{ old('business_name') }}" required></div>
                <div class="col-md-6"><label class="form-label">Contact person</label><input class="form-control" name="contact_person" value="{{ old('contact_person') }}" required></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone') }}" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="col-md-6">
                    <label class="form-label">Primary city</label>
                    <select class="form-select" name="city_id">
                        <option value="">Select current city</option>
                        @foreach($cities as $city)<option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>{{ $city->name }}{{ $city->state ? ', '.$city->state : '' }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Other city</label><input class="form-control" name="city" value="{{ old('city') }}" placeholder="Use if city is not listed"></div>
                <div class="col-md-6"><label class="form-label">State</label><input class="form-control" name="state" value="{{ old('state') }}" placeholder="Maharashtra, Delhi, Haryana"></div>
                <div class="col-md-6"><label class="form-label">Coverage areas</label><input class="form-control" name="coverage_areas" value="{{ old('coverage_areas') }}" placeholder="Dwarka, Rohini, Sector 62"></div>
                <div class="col-12"><label class="form-label">Full address</label><textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea></div>
                <div class="col-12">
                    <label class="form-label">Services you can handle</label>
                    <select class="form-select" name="service_ids[]" multiple size="8" required>
                        @foreach($services as $service)<option value="{{ $service->id }}" @selected(in_array($service->id, old('service_ids', [])))>{{ $service->title }}</option>@endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple services.</small>
                </div>
                <div class="col-md-4"><label class="form-label">Account holder</label><input class="form-control" name="account_name" value="{{ old('account_name') }}"></div>
                <div class="col-md-4"><label class="form-label">Account number</label><input class="form-control" name="account_number" value="{{ old('account_number') }}"></div>
                <div class="col-md-4"><label class="form-label">IFSC</label><input class="form-control" name="ifsc" value="{{ old('ifsc') }}"></div>
                <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <div class="col-md-6"><label class="form-label">Confirm password</label><input class="form-control" type="password" name="password_confirmation" required></div>
            </div>
            <button class="btn btn-party w-100 mt-4">Submit vendor profile</button>
        </form>
        <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Login</a></p>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="auth-card wide">
        <span class="eyebrow">Customer</span>
        <h1>Create account</h1>
        <a class="google-auth-button" href="{{ route('auth.google.redirect') }}"><i class="fa-brands fa-google"></i><span>Continue with Google</span></a>
        <div class="auth-divider"><span>or register with email</span></div>
        <form method="post" action="{{ route('register.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name') }}" required></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone') }}" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="col-md-6"><label class="form-label">City</label><input class="form-control" name="city" value="{{ old('city') }}" placeholder="Delhi, Noida, Gurgaon"></div>
                <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <div class="col-md-6"><label class="form-label">Confirm Password</label><input class="form-control" type="password" name="password_confirmation" required></div>
            </div>
            <button class="btn btn-party w-100 mt-4" type="submit">Register</button>
        </form>
        <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Login</a></p>
        <p class="auth-switch">Want to receive local party jobs? <a href="{{ route('vendors.register') }}">Register as vendor</a></p>
    </div>
</section>
@endsection

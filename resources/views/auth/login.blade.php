@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="auth-card">
        <span class="eyebrow">{{ $isAdminLogin ?? false ? 'Admin' : 'Customer' }}</span>
        <h1>{{ $isAdminLogin ?? false ? 'Admin Login' : 'Customer Login' }}</h1>
        @unless($isAdminLogin ?? false)
            <a class="google-auth-button" href="{{ route('auth.google.redirect') }}"><i class="fa-brands fa-google"></i><span>Continue with Google</span></a>
            <div class="auth-divider"><span>or continue with email</span></div>
        @endunless
        <form method="post" action="{{ $isAdminLogin ?? false ? route('admin.login.store') : route('login.store') }}">
            @csrf
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <label class="form-label mt-3">Password</label>
            <input class="form-control" type="password" name="password" required>
            <label class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="remember" value="1">
                <span class="form-check-label">Remember me</span>
            </label>
            <button class="btn btn-party w-100 mt-4" type="submit">Login</button>
        </form>
        @unless($isAdminLogin ?? false)
            <p class="auth-switch">New here? <a href="{{ route('register') }}">Create customer account</a></p>
            <p class="auth-switch">Party service provider? <a href="{{ route('vendors.register') }}">Register as vendor</a></p>
        @endunless
    </div>
</section>
@endsection

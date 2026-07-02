@extends('layouts.app')

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-welcome">
            <div><span class="mini-label">Customer dashboard</span><h1>My profile</h1><p class="section-lead">Keep contact and venue details ready for checkout.</p></div>
            <a class="btn btn-outline-party" href="{{ route('dashboard') }}">Back to dashboard</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="dashboard-panel">
                    <form method="post" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="profile-editor-head">
                            <div class="profile-avatar-preview">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                                @else
                                    <i class="fa-regular fa-user"></i>
                                @endif
                            </div>
                            <div>
                                <span class="mini-label">Account details</span>
                                <h2>{{ $user->name }}</h2>
                                <p>{{ $user->email }}</p>
                                @if($user->google_id)<span class="google-account-badge"><i class="fa-brands fa-google"></i> Google login enabled</span>@endif
                            </div>
                        </div>

                        @if($user->pending_email)
                            <div class="dashboard-alert compact">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                                <div><strong>Email verification pending</strong><span>We sent a verification link to {{ $user->pending_email }}.</span></div>
                                <button class="btn btn-outline-party btn-sm" form="resend-email-verification">Resend</button>
                            </div>
                        @endif

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                <small class="form-text text-muted">Changing email requires verification before it becomes active.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile image</label>
                                <input class="form-control" type="file" name="avatar" accept="image/*">
                                <small class="form-text text-muted">Recommended 600 x 600 px. JPG, PNG or WebP, max 2 MB.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default city</label>
                                <input class="form-control" name="city" value="{{ old('city', $user->city) }}" placeholder="Delhi, Noida or Gurgaon">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Default address</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Full house, venue, sector and landmark">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>

                        <button class="btn btn-party mt-4">Save profile</button>
                    </form>

                    @if($user->pending_email)
                        <form id="resend-email-verification" method="post" action="{{ route('dashboard.profile.email.resend') }}" class="d-none">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Reviews</span>
        <h1>What parents say</h1>
        <p>Approved reviews from families who booked kids birthday activities.</p>
    </div>
</section>
<section class="section section-soft">
    <div class="container">
        <div class="row g-4">
            @forelse($reviews as $review)
                <div class="col-md-6 col-xl-4">
                    <div class="testimonial-card h-100">
                        <div class="stars">@for($i = 0; $i < $review->rating; $i++)<i class="fa-solid fa-star"></i>@endfor</div>
                        <p>{{ $review->comment }}</p>
                        <strong>{{ $review->customer_name }}</strong>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="empty-state">No approved reviews yet.</div></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</section>

@auth
<section class="section">
    <div class="container">
        <div class="booking-panel mx-auto" style="max-width:760px">
            <h2>Share your experience</h2>
            <form action="{{ route('reviews.store') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="customer_name" value="{{ auth()->user()->name }}" required></div>
                    <div class="col-md-6"><label class="form-label">Service</label><select class="form-select" name="service_id"><option value="">General review</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->title }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Rating</label><select class="form-select" name="rating">@for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} stars</option>@endfor</select></div>
                    <div class="col-12"><label class="form-label">Comment</label><textarea class="form-control" name="comment" rows="4" required></textarea></div>
                </div>
                <button class="btn btn-party mt-4">Submit Review</button>
            </form>
        </div>
    </div>
</section>
@endauth
@endsection

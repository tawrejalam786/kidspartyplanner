@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Gallery</span>
        <h1>Party snapshots</h1>
        <p>Activity corners, entertainment moments, games, and celebration setups.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="gallery-grid large">
            @foreach($gallery as $item)
                <figure>
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">
                    <figcaption>{{ $item->title }} <span>{{ $item->type }}</span></figcaption>
                </figure>
            @endforeach
        </div>
        <div class="mt-4">{{ $gallery->links() }}</div>
    </div>
</section>
@endsection

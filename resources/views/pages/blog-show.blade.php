@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Blog</span>
        <h1>{{ $blog->title }}</h1>
        <p>{{ $blog->excerpt }}</p>
    </div>
</section>
<section class="section">
    <div class="container narrow-content">
        <img class="detail-main-image mb-4" src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy">
        <div class="content-body">{!! $blog->content !!}</div>
    </div>
</section>
@endsection

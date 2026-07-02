@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Blog</span>
        <h1>Party planning ideas</h1>
        <p>Guides for choosing kids birthday entertainment, activities, and schedules.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-4">
            @foreach($blogs as $blog)
                <div class="col-md-6 col-xl-4">
                    <a class="blog-card" href="{{ route('blog.show', $blog) }}">
                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy">
                        <div><span>{{ optional($blog->published_at)->format('d M Y') }}</span><h2>{{ $blog->title }}</h2><p>{{ $blog->excerpt }}</p></div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $blogs->links() }}</div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<section class="page-hero marketplace-hero"><div class="container"><span class="eyebrow">Explore celebrations</span><h1>Everything a great party needs.</h1><p>Start with a category, narrow to a style, then book a service for {{ $selectedCity?->name ?? 'your city' }}.</p></div></section>
<section class="section"><div class="container"><div class="category-directory">@foreach($categories as $category)<article class="directory-block"><a class="directory-media" href="{{ route('categories.show', $category) }}"><img src="{{ $category->image_url }}" alt="{{ $category->name }}"><span>{{ $category->services_count }} services</span></a><div class="directory-copy"><span class="mini-label">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h2><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></h2><p>{{ $category->description }}</p><div class="subcategory-links">@foreach($category->subcategories as $subcategory)<a href="{{ route('subcategories.show', $subcategory) }}">{{ $subcategory->name }} <i class="fa-solid fa-arrow-right"></i></a>@endforeach</div></div></article>@endforeach</div></div></section>
@endsection

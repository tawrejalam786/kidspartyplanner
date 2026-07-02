@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Policy</span>
        <h1>{{ $page->title }}</h1>
    </div>
</section>
<section class="section">
    <div class="container narrow-content">
        <div class="content-body">{!! $page->content !!}</div>
    </div>
</section>
@endsection

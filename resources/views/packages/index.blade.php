@extends('layouts.app')

@section('content')
<section class="page-hero compact-hero">
    <div class="container">
        <span class="eyebrow">Packages</span>
        <h1>Kids Birthday Party Packages</h1>
        <p>Curated combinations of entertainment, games, crafts, and coordination for easier planning.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="row g-4">
            @foreach($packages as $package)
                <div class="col-md-6 col-xl-4">@include('partials.package-card', ['package' => $package])</div>
            @endforeach
        </div>
        <div class="mt-4">{{ $packages->links() }}</div>
    </div>
</section>
@endsection

<div class="row g-3 g-md-4">
    @forelse($services as $service)
        <div class="col-6 col-xl-4">
            @include('partials.service-card', ['service' => $service])
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">No services matched these filters.</div>
        </div>
    @endforelse
</div>
<div class="mt-4 ajax-pagination">
    {{ $services->links() }}
</div>

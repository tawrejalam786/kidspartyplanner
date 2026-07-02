@extends('layouts.admin')

@section('title', $config['title'])

@section('content')
<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 flex-wrap">
        <div><h2 class="mb-0">{{ $config['title'] }}</h2><small class="text-muted">Search records, then open the edit action.</small></div>
        <a href="{{ route('admin.resources.create', $resource) }}" class="btn btn-party btn-sm"><i class="fa-solid fa-plus"></i> Add {{ $config['singular'] }}</a>
    </div>
    <form class="admin-search-form" method="get" action="{{ route('admin.resources.index', $resource) }}">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="search" value="{{ $search }}" placeholder="Search {{ strtolower($config['title']) }}" aria-label="Search {{ $config['title'] }}">
        @if($search !== '')<a href="{{ route('admin.resources.index', $resource) }}" aria-label="Clear search"><i class="fa-solid fa-xmark"></i></a>@endif
        <button class="btn btn-dark btn-sm" type="submit">Search</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    @foreach($config['columns'] as $column)
                        <th>{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $column)) }}</th>
                    @endforeach
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        @foreach($config['columns'] as $column)
                            @php($value = data_get($item, $column))
                            <td>
                                @if(in_array($column, ['image', 'path'], true) && $value)
                                    <img class="admin-table-thumb" src="{{ \Illuminate\Support\Str::startsWith($value, ['http://', 'https://']) ? $value : asset('storage/'.$value) }}" alt="">
                                @elseif(is_bool($value))
                                    <span class="badge {{ $value ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                @elseif($value instanceof \Carbon\CarbonInterface)
                                    {{ $value->format('d M Y') }}
                                @elseif(is_array($value))
                                    {{ \Illuminate\Support\Str::limit(json_encode($value), 60) }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) $value, 80) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end">
                            <a class="btn btn-outline-party btn-sm" href="{{ route('admin.resources.edit', [$resource, $item->id]) }}"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('admin.resources.destroy', [$resource, $item->id]) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($config['columns']) + 1 }}" class="text-center py-5">{{ $search !== '' ? 'No records matched “'.$search.'”.' : 'No records found.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection

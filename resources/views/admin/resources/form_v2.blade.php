@extends('layouts.admin')

@section('title', ($item ? 'Edit ' : 'Add ').$config['singular'])

@section('content')
<div class="admin-card">
    <form method="post" enctype="multipart/form-data" action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}">
        @csrf
        @if($item) @method('PUT') @endif
        <div class="row g-3">
            @foreach($config['fields'] as $field)
                <?php
                    $name = $field['name'];
                    $type = $field['type'];
                    $value = old($name, $item ? data_get($item, $name) : null);
                    if ($type === 'password') {
                        $value = null;
                    }
                    if (is_array($value)) {
                        $value = json_encode($value, JSON_PRETTY_PRINT);
                    }
                    if ($value instanceof \Carbon\CarbonInterface) {
                        $value = in_array($type, ['datetime'], true) ? $value->format('Y-m-d\TH:i') : $value->format('Y-m-d');
                    }
                ?>
                <div class="{{ in_array($type, ['textarea', 'json'], true) ? 'col-12' : 'col-md-6' }}">
                    @if($type === 'checkbox')
                        <label class="form-check admin-check">
                            <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $item ? (bool) data_get($item, $name) : ($field['default'] ?? true)))>
                            <span class="form-check-label">{{ $field['label'] }}</span>
                        </label>
                    @elseif($type === 'select')
                        <label class="form-label">{{ $field['label'] }}</label>
                        <select class="form-select" name="{{ $name }}">
                            @foreach($field['options'] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @elseif($type === 'multiselect')
                        <?php $selectedValues = old($name, $item ? $item->{$field['relation']}->pluck('id')->map(fn($id)=>(string)$id)->all() : []); ?>
                        <label class="form-label">{{ $field['label'] }}</label>
                        <select class="form-select" name="{{ $name }}[]" multiple size="6">
                            @foreach($field['options'] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected(in_array((string)$optionValue, array_map('strval', $selectedValues ?? []), true))>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @elseif($type === 'textarea' || $type === 'json')
                        <label class="form-label">{{ $field['label'] }}</label>
                        <textarea class="form-control" name="{{ $name }}" rows="{{ $type === 'json' ? 7 : 4 }}">{{ $value }}</textarea>
                    @elseif($type === 'file')
                        <label class="form-label">{{ $field['label'] }}</label>
                        <?php
                            $recommendedSize = match($field['folder'] ?? '') {
                                'services' => '1200 x 600 px',
                                'banners' => '1600 x 700 px',
                                'categories', 'subcategories', 'blogs' => '900 x 700 px',
                                'addons' => '600 x 600 px',
                                'gallery' => '1200 x 900 px',
                                default => '1200 x 800 px',
                            };
                            $currentImagePath = null;
                            if ($item) {
                                if (($field['virtual'] ?? false) && $resource === 'services' && $name === 'primary_image') {
                                    $currentImagePath = optional($item->primaryImage)->path ?: optional($item->images->first())->path;
                                } elseif (! ($field['virtual'] ?? false)) {
                                    $currentImagePath = $value;
                                }
                            }
                            $currentImageUrl = $currentImagePath
                                ? (\Illuminate\Support\Str::startsWith($currentImagePath, ['http://','https://']) ? $currentImagePath : asset('storage/'.$currentImagePath))
                                : (($field['virtual'] ?? false) && $item ? ($item->image_url ?? null) : null);
                        ?>
                        @if($item && $currentImageUrl)
                            <div class="admin-current-image">
                                <span>Current image</span>
                                <a href="{{ $currentImageUrl }}" target="_blank" rel="noopener">
                                    <img class="admin-preview" src="{{ $currentImageUrl }}" alt="{{ $field['label'] }}">
                                </a>
                                @if($currentImagePath)
                                    <small>{{ $currentImagePath }}</small>
                                @endif
                            </div>
                        @endif
                        <input class="form-control" type="file" name="{{ $name }}" accept="image/*" @if(($field['required'] ?? false) && ! $item) required @endif>
                        <small class="form-text text-muted">
                            Recommended {{ $recommendedSize }}. JPG, PNG or WebP, maximum 2 MB.
                            @if($item) Leave blank to keep current image. @endif
                        </small>
                        @if($field['help'] ?? false)
                            <small class="form-text text-muted d-block">{{ $field['help'] }}</small>
                        @endif
                        @if($item && ! $currentImageUrl)
                            <div class="admin-current-image is-empty">
                                <span>No current image found</span>
                                <small>Upload an image to add one.</small>
                            </div>
                        @endif
                    @else
                        <label class="form-label">{{ $field['label'] }}</label>
                        <input class="form-control" type="{{ $type === 'datetime' ? 'datetime-local' : $type }}" name="{{ $name }}" value="{{ $value }}" @if($field['required'] ?? false) required @endif>
                        @if($field['help'] ?? false)
                            <small class="form-text text-muted">{{ $field['help'] }}</small>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-party" type="submit">Save {{ $config['singular'] }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.resources.index', $resource) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection

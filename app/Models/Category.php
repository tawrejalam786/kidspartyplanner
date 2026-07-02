<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'image', 'is_active', 'sort_order', 'meta_title', 'meta_description', 'meta_keywords', 'og_image'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=900&q=80';
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/'.$this->image);
    }
}

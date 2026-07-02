<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'published_at', 'is_active', 'meta_title', 'meta_description', 'meta_keywords', 'og_image'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return 'https://images.unsplash.com/photo-1464349153735-7db50ed83c84?auto=format&fit=crop&w=900&q=80';
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/'.$this->image);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'image', 'is_active', 'sort_order', 'meta_title', 'meta_description', 'meta_keywords', 'og_image'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function getRouteKeyName(): string { return 'slug'; }
    public function category() { return $this->belongsTo(Category::class); }
    public function services() { return $this->hasMany(Service::class); }
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? (Str::startsWith($this->image, ['http://', 'https://']) ? $this->image : asset('storage/'.$this->image))
            : 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=900&q=80';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Addon extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'price', 'image', 'is_active'];
    protected function casts(): array { return ['price' => 'decimal:2', 'is_active' => 'boolean']; }
    public function services() { return $this->belongsToMany(Service::class)->withPivot('price_override'); }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return self::fallbackImageUrl();
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/'.$this->image);
    }

    public static function fallbackImageUrl(): string
    {
        return 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=500&q=82';
    }
}

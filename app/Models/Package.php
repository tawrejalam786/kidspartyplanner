<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'discount_price',
        'services',
        'inclusions',
        'image',
        'duration',
        'featured',
        'trending',
        'terms',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'services' => 'array',
            'inclusions' => 'array',
            'featured' => 'boolean',
            'trending' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function includedServices()
    {
        return $this->belongsToMany(Service::class)->withPivot('quantity');
    }

    public function cities()
    {
        return $this->belongsToMany(City::class)->withPivot('price_override');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?: $this->price);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return 'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=900&q=80';
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/'.$this->image);
    }
}

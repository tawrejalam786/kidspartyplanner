<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'discount_price',
        'duration',
        'age_group',
        'kids_capacity',
        'location',
        'rating',
        'total_reviews',
        'inclusions',
        'exclusions',
        'requirements',
        'terms',
        'cancellation_policy',
        'video_url',
        'advance_percent',
        'add_ons',
        'faq',
        'featured',
        'trending',
        'sort_order',
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
            'rating' => 'decimal:1',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'requirements' => 'array',
            'add_ons' => 'array',
            'faq' => 'array',
            'featured' => 'boolean',
            'trending' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function cityPrices()
    {
        return $this->hasMany(ServiceCityPrice::class);
    }

    public function availableCities()
    {
        return $this->belongsToMany(City::class, 'service_city_prices')->withPivot(['price', 'sale_price', 'advance_percent', 'travel_fee', 'is_available']);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class)->withPivot('price_override');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class)
            ->orderByDesc('is_primary')
            ->latest('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?: $this->price);
    }

    public function priceForCity(?City $city): float
    {
        if (! $city) {
            return $this->effective_price;
        }

        $price = $this->relationLoaded('cityPrices')
            ? $this->cityPrices->firstWhere('city_id', $city->id)
            : $this->cityPrices()->where('city_id', $city->id)->first();

        return $price?->effective_price ?? $this->effective_price;
    }

    public function getImageUrlAttribute(): string
    {
        $path = optional($this->primaryImage)->path ?: optional($this->images->first())->path;

        if (! $path) {
            return 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=900&q=80';
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/'.$path);
    }
}

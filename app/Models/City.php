<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'slug', 'state', 'image', 'is_current', 'is_active', 'sort_order', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return ['is_current' => 'boolean', 'is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function areas() { return $this->hasMany(Area::class); }
    public function servicePrices() { return $this->hasMany(ServiceCityPrice::class); }
    public function packages() { return $this->belongsToMany(Package::class)->withPivot('price_override'); }
    public function bookings() { return $this->hasMany(Booking::class); }
}

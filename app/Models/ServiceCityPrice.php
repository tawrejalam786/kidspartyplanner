<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCityPrice extends Model
{
    protected $fillable = ['service_id', 'city_id', 'price', 'sale_price', 'advance_percent', 'travel_fee', 'is_available'];
    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'sale_price' => 'decimal:2', 'advance_percent' => 'decimal:2', 'travel_fee' => 'decimal:2', 'is_available' => 'boolean'];
    }
    public function service() { return $this->belongsTo(Service::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function getEffectivePriceAttribute(): float { return (float) ($this->sale_price ?: $this->price); }
}

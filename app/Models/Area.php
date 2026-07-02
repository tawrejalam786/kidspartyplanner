<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['city_id', 'name', 'slug', 'pincode', 'travel_fee', 'is_active'];
    protected function casts(): array { return ['travel_fee' => 'decimal:2', 'is_active' => 'boolean']; }
    public function city() { return $this->belongsTo(City::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = ['booking_id', 'service_id', 'package_id', 'item_name', 'item_type', 'quantity', 'unit_price', 'line_total'];
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2']; }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function addons() { return $this->hasMany(BookingAddon::class); }
}

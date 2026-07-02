<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
    protected $fillable = ['booking_id', 'booking_item_id', 'addon_id', 'name', 'price', 'quantity'];
    protected function casts(): array { return ['price' => 'decimal:2']; }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function bookingItem() { return $this->belongsTo(BookingItem::class); }
    public function addon() { return $this->belongsTo(Addon::class); }
}

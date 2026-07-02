<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['booking_id', 'payment_id', 'amount', 'status', 'reason', 'gateway_refund_id', 'admin_note'];
    protected function casts(): array { return ['amount' => 'decimal:2']; }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
}

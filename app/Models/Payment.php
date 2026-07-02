<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'razorpay_order_id',
        'payment_id',
        'signature',
        'amount',
        'currency',
        'status',
        'payment_status',
        'refunded_amount',
        'method',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'raw_response' => 'array',
            'refunded_amount' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}

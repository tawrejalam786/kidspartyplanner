<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorEarning extends Model
{
    protected $fillable = [
        'vendor_id',
        'booking_assignment_id',
        'booking_id',
        'gross_amount',
        'commission_amount',
        'net_amount',
        'status',
        'available_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'available_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function assignment() { return $this->belongsTo(BookingAssignment::class, 'booking_assignment_id'); }
    public function booking() { return $this->belongsTo(Booking::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAssignment extends Model
{
    protected $fillable = [
        'booking_id',
        'vendor_id',
        'assigned_by',
        'status',
        'assigned_amount',
        'platform_commission',
        'vendor_earning',
        'assigned_at',
        'accepted_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_amount' => 'decimal:2',
            'platform_commission' => 'decimal:2',
            'vendor_earning' => 'decimal:2',
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function assignedBy() { return $this->belongsTo(User::class, 'assigned_by'); }
    public function earning() { return $this->hasOne(VendorEarning::class); }
}

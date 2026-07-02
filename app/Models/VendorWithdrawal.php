<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorWithdrawal extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'status',
        'bank_details',
        'payout_reference',
        'admin_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'bank_details' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function vendor() { return $this->belongsTo(Vendor::class); }
}

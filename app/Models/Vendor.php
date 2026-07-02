<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'business_name',
        'slug',
        'contact_person',
        'phone',
        'email',
        'city',
        'state',
        'address',
        'coverage_areas',
        'bank_details',
        'documents',
        'commission_percent',
        'wallet_balance',
        'status',
        'admin_note',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'coverage_areas' => 'array',
            'bank_details' => 'array',
            'documents' => 'array',
            'commission_percent' => 'decimal:2',
            'wallet_balance' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function cityModel() { return $this->belongsTo(City::class, 'city_id'); }
    public function services() { return $this->belongsToMany(Service::class); }
    public function assignments() { return $this->hasMany(BookingAssignment::class); }
    public function earnings() { return $this->hasMany(VendorEarning::class); }
    public function withdrawals() { return $this->hasMany(VendorWithdrawal::class); }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }
}

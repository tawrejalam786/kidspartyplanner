<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityPaymentSetting extends Model
{
    protected $fillable = [
        'city',
        'slug',
        'advance_percent',
        'minimum_advance',
        'service_fee',
        'tax_percent',
        'razorpay_key_id',
        'razorpay_key_secret',
        'razorpay_webhook_secret',
        'payment_instructions',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'advance_percent' => 'decimal:2',
            'minimum_advance' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'razorpay_key_secret' => 'encrypted',
            'razorpay_webhook_secret' => 'encrypted',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function gatewayKey(): ?string
    {
        return $this->razorpay_key_id ?: config('services.razorpay.key');
    }

    public function gatewaySecret(): ?string
    {
        return $this->razorpay_key_secret ?: config('services.razorpay.secret');
    }

    public function webhookSecret(): ?string
    {
        return $this->razorpay_webhook_secret ?: config('services.razorpay.webhook_secret');
    }
}

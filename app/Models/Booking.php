<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_no',
        'user_id',
        'service_id',
        'package_id',
        'city_payment_setting_id',
        'city_id',
        'area_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'event_date',
        'event_time',
        'location',
        'area_name',
        'full_address',
        'landmark',
        'event_type',
        'age_group',
        'venue_type',
        'decoration_theme',
        'number_of_kids',
        'add_ons',
        'message',
        'status',
        'workflow_status',
        'payment_status',
        'tracking_status',
        'invoice_no',
        'cancellation_reason',
        'cancelled_at',
        'confirmation_emailed_at',
        'payment_type',
        'base_amount',
        'service_fee',
        'tax_amount',
        'total_amount',
        'advance_amount',
        'payable_amount',
        'coupon_code',
        'coupon_discount',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'add_ons' => 'array',
            'base_amount' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'payable_amount' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'confirmation_emailed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function cityPaymentSetting()
    {
        return $this->belongsTo(CityPaymentSetting::class);
    }

    public function city() { return $this->belongsTo(City::class); }
    public function area() { return $this->belongsTo(Area::class); }
    public function items() { return $this->hasMany(BookingItem::class); }
    public function bookingAddons() { return $this->hasMany(BookingAddon::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
    public function assignments() { return $this->hasMany(BookingAssignment::class); }
    public function latestAssignment() { return $this->hasOne(BookingAssignment::class)->latestOfMany(); }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getItemTitleAttribute(): string
    {
        return optional($this->service)->title ?: optional($this->package)->title ?: optional($this->items->first())->item_name ?: 'Custom Party Booking';
    }
}

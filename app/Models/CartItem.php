<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'service_id', 'package_id', 'quantity', 'unit_price', 'selected_addons'];
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'selected_addons' => 'array']; }
    public function cart() { return $this->belongsTo(Cart::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function getTitleAttribute(): string { return $this->service?->title ?: $this->package?->title ?: 'Party item'; }
    public function getLineTotalAttribute(): float
    {
        return ((float) $this->unit_price * $this->quantity) + collect($this->selected_addons ?? [])->sum(fn ($addon) => ((float) ($addon['price'] ?? 0)) * ((int) ($addon['quantity'] ?? 1)));
    }
}

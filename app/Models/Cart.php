<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'city_id', 'session_token'];
    public function user() { return $this->belongsTo(User::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function items() { return $this->hasMany(CartItem::class); }
    public function getSubtotalAttribute(): float { return (float) $this->items->sum(fn ($item) => $item->line_total); }
}

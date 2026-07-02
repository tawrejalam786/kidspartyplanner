<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'service_id', 'package_id', 'customer_name', 'rating', 'comment', 'is_approved'];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
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
}

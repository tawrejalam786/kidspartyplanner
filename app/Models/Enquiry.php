<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = ['service_id', 'name', 'email', 'phone', 'subject', 'message', 'source', 'status'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

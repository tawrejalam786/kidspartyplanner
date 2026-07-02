<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceImage extends Model
{
    protected $fillable = ['service_id', 'path', 'alt_text', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getUrlAttribute(): string
    {
        return Str::startsWith($this->path, ['http://', 'https://'])
            ? $this->path
            : asset('storage/'.$this->path);
    }
}

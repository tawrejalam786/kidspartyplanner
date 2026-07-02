<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $fillable = ['title', 'subtitle', 'image', 'button_text', 'button_url', 'placement', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function getImageUrlAttribute(): string { return Str::startsWith($this->image, ['http://', 'https://']) ? $this->image : asset('storage/'.$this->image); }
}

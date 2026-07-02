<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        return Cache::rememberForever('setting_'.$key, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'is_public'];

    protected $casts = [
        'value' => 'json',
        'is_public' => 'boolean',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, string $group = 'general', bool $isPublic = true)
    {
        return static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'is_public' => $isPublic]
        );
    }
}


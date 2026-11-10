<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
        'model',
        'is_enabled',
        'daily_request_limit',
        'today_requests_count',
        'last_reset_date',
        'options',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'daily_request_limit' => 'integer',
        'today_requests_count' => 'integer',
        'last_reset_date' => 'date',
        'options' => 'array',
        'api_key' => 'encrypted',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'provider' => config('services.ai.provider', 'gemini'),
            'model' => config('services.ai.model', 'gemini-1.5-flash'),
            'is_enabled' => false,
            'daily_request_limit' => 200,
        ]);
    }
}

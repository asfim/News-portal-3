<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAccount extends Model
{
    protected $fillable = [
        'provider',
        'account_id',
        'account_name',
        'access_token',
        'token_expires_at',
        'is_active',
        'auto_publish',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_publish' => 'boolean',
        'token_expires_at' => 'datetime',
        'settings' => 'array',
        'access_token' => 'encrypted',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class, 'social_account_id')->orderByDesc('created_at');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialPost extends Model
{
    protected $fillable = [
        'social_account_id',
        'news_id',
        'idempotency_key',
        'post_text',
        'media_url',
        'external_post_id',
        'scheduled_at',
        'published_at',
        'status',
        'retry_count',
        'last_error',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SocialPostLog::class, 'social_post_id');
    }
}

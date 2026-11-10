<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPostLog extends Model
{
    protected $fillable = [
        'social_post_id',
        'action',
        'status',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'response_payload' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'social_post_id');
    }
}

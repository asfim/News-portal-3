<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotificationLog extends Model
{
    protected $fillable = [
        'news_id',
        'title',
        'body',
        'url',
        'icon_url',
        'image_url',
        'target_subscribers',
        'successful_sent',
        'failed_count',
        'sent_by',
        'status',
    ];

    protected $casts = [
        'target_subscribers' => 'integer',
        'successful_sent' => 'integer',
        'failed_count' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}

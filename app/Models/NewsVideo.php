<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsVideo extends Model
{
    protected $fillable = [
        'news_id',
        'provider',
        'provider_video_id',
        'media_id',
        'thumbnail_media_id',
        'duration_seconds',
        'transcript',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function thumbnailMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }
}


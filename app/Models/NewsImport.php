<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsImport extends Model
{
    protected $fillable = [
        'source_id',
        'news_id',
        'guid',
        'source_url',
        'url_hash',
        'content_fingerprint',
        'title',
        'summary',
        'image_url',
        'published_at',
        'status',
        'duplicate_of_news_id',
        'error_message',
        'raw_payload',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class, 'source_id');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function duplicateOfNews(): BelongsTo
    {
        return $this->belongsTo(News::class, 'duplicate_of_news_id');
    }
}

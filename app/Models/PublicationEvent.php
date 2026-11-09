<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationEvent extends Model
{
    protected $fillable = [
        'news_id',
        'revision_id',
        'event_type',
        'payload',
        'processed_at',
        'attempts',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}


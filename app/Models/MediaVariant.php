<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaVariant extends Model
{
    protected $fillable = [
        'media_id',
        'variant',
        'path',
        'width',
        'height',
        'format',
        'size_bytes',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        return Storage::disk($this->media->disk ?? 'public')->url($this->path);
    }
}


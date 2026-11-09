<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'uploaded_by',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'checksum',
        'alt_text',
        'caption',
        'credit',
        'license_note',
        'status',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return '';
        }

        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        if (str_starts_with($this->path, '/')) {
            return asset(ltrim($this->path, '/'));
        }

        if (str_starts_with($this->path, 'storage/')) {
            return asset($this->path);
        }

        return Storage::disk($this->disk ?? 'public')->url($this->path);
    }
}


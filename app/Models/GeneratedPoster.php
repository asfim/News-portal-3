<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedPoster extends Model
{
    protected $fillable = [
        'news_id',
        'template_id',
        'headline_text',
        'file_path',
        'dimensions',
        'file_size_bytes',
        'generated_by',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PosterTemplate::class, 'template_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
    }
}

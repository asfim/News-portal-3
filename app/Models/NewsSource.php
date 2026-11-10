<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsSource extends Model
{
    protected $fillable = [
        'name',
        'feed_url',
        'type',
        'default_category_id',
        'language',
        'fetch_interval_minutes',
        'last_fetched_at',
        'is_active',
        'auto_publish',
        'author_attribution',
        'headers',
        'settings',
        'consecutive_failures',
        'last_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_publish' => 'boolean',
        'last_fetched_at' => 'datetime',
        'headers' => 'array',
        'settings' => 'array',
        'fetch_interval_minutes' => 'integer',
        'consecutive_failures' => 'integer',
    ];

    public function defaultCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'default_category_id');
    }

    public function imports(): HasMany
    {
        return $this->hasMany(NewsImport::class, 'source_id')->orderByDesc('created_at');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsImportLog::class, 'source_id')->orderByDesc('created_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

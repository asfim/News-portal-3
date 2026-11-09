<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakingItem extends Model
{
    protected $fillable = [
        'news_id',
        'label',
        'url',
        'starts_at',
        'ends_at',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('priority');
    }
}


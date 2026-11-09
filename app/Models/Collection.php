<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'collection_news')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}


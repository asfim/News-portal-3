<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_tag');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('sort_order');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSectionItem extends Model
{
    protected $fillable = [
        'section_id',
        'news_id',
        'position',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'position' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(HomepageSection::class, 'section_id');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}


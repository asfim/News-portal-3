<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsDailyStat extends Model
{
    protected $fillable = [
        'news_id',
        'stat_date',
        'views',
        'unique_views',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'views' => 'integer',
        'unique_views' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}


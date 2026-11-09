<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsStatusHistory extends Model
{
    protected $fillable = [
        'news_id',
        'from_status',
        'to_status',
        'actor_id',
        'reason',
        'revision_id',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}


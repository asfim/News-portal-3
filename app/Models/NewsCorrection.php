<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCorrection extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'news_id',
        'revision_id',
        'public_note',
        'corrected_by',
        'corrected_at',
    ];

    protected $casts = [
        'corrected_at' => 'datetime',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsRevision extends Model
{
    protected $fillable = [
        'news_id',
        'version',
        'snapshot',
        'status',
        'edited_by',
        'approved_by',
        'approved_at',
        'change_note',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'approved_at' => 'datetime',
        'version' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}


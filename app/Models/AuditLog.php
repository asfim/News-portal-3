<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'before',
        'after',
        'request_id',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}


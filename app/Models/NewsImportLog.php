<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsImportLog extends Model
{
    protected $fillable = [
        'source_id',
        'status',
        'items_found',
        'items_imported',
        'items_skipped_duplicate',
        'items_failed',
        'execution_time_seconds',
        'message',
    ];

    protected $casts = [
        'items_found' => 'integer',
        'items_imported' => 'integer',
        'items_skipped_duplicate' => 'integer',
        'items_failed' => 'integer',
        'execution_time_seconds' => 'float',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class, 'source_id');
    }
}

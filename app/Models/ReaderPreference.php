<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReaderPreference extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'category_affinity',
        'updated_at',
    ];

    protected $casts = [
        'category_affinity' => 'array',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

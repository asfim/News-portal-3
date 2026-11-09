<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'media_id',
        'destination_url',
        'provider_type',
        'provider_slot_id',
        'starts_at',
        'ends_at',
        'priority',
        'status',
        'sponsor_name',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'priority' => 'integer',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(AdSlot::class, 'advertisement_slot');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'advertisement_category');
    }
}


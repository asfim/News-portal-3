<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdSlot extends Model
{
    protected $fillable = [
        'key',
        'name',
        'desktop_width',
        'desktop_height',
        'mobile_width',
        'mobile_height',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function advertisements(): BelongsToMany
    {
        return $this->belongsToMany(Advertisement::class, 'advertisement_slot');
    }

    public function activeAdvertisements(): BelongsToMany
    {
        return $this->advertisements()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('priority');
    }
}


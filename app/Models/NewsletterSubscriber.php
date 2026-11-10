<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'token',
        'verified_at',
        'is_active',
        'unsubscribed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(NewsletterDelivery::class, 'subscriber_id');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at')->where('is_active', true);
    }
}

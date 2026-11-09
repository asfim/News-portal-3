<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuthorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'bio',
        'designation',
        'avatar_path',
        'social_links',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_author')
            ->withPivot('sort_order', 'role')
            ->orderByPivot('sort_order');
    }
}


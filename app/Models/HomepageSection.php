<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSection extends Model
{
    protected $fillable = [
        'key',
        'title',
        'layout',
        'source_type',
        'category_id',
        'collection_id',
        'item_limit',
        'sort_order',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'item_limit' => 'integer',
        'settings' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(HomepageSectionItem::class, 'section_id')->orderBy('position');
    }
}


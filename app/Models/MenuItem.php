<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'target_type',
        'category_id',
        'page_id',
        'collection_id',
        'external_url',
        'sort_order',
        'open_new_tab',
        'is_active',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->target_type === 'custom' && $this->external_url) {
            return $this->external_url;
        }

        if ($this->target_type === 'category' && $this->category) {
            return url('/category/' . $this->category->slug);
        }

        if ($this->target_type === 'page' && $this->page) {
            return url('/page/' . $this->page->slug);
        }

        if ($this->target_type === 'collection' && $this->collection) {
            return url('/collection/' . $this->collection->slug);
        }

        return '#';
    }
}


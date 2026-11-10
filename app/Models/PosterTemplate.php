<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosterTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'dimensions',
        'background_color',
        'headline_color',
        'category_badge_color',
        'overlay_opacity',
        'font_family',
        'logo_path',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'overlay_opacity' => 'float',
    ];

    public function generatedPosters(): HasMany
    {
        return $this->hasMany(GeneratedPoster::class, 'template_id');
    }
}

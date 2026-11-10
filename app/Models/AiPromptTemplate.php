<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplate extends Model
{
    protected $fillable = [
        'key',
        'title',
        'system_prompt',
        'user_prompt_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

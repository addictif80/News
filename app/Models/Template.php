<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'type', 'name', 'is_default', 'html', 'css', 'grapesjs_data',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'grapesjs_data' => 'array',
        ];
    }
}

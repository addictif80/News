<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceSite extends Model
{
    protected $fillable = [
        'name', 'base_url', 'rss_feed_url', 'logo', 'is_active', 'used_for_watch', 'last_polled_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'used_for_watch' => 'boolean',
            'last_polled_at' => 'datetime',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}

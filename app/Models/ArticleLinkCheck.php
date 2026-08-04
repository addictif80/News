<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleLinkCheck extends Model
{
    protected $fillable = [
        'article_id', 'url', 'type', 'status_code', 'is_broken', 'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_broken' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}

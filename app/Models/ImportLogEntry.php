<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLogEntry extends Model
{
    protected $fillable = [
        'source_site_id', 'article_id', 'keyword', 'url', 'outcome', 'message',
    ];

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(SourceSite::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}

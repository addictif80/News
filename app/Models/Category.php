<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'description', 'is_featured_on_homepage', 'position',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ContentCache::bump());
        static::deleted(fn () => ContentCache::bump());
    }

    protected function casts(): array
    {
        return [
            'is_featured_on_homepage' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }
}

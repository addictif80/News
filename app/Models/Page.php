<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasSlug, LogsActivity;

    protected array $activityLogExcept = ['content'];

    protected $fillable = [
        'template_id', 'title', 'slug', 'featured_image', 'content', 'status', 'published_at',
        'seo_title', 'seo_description', 'seo_og_image', 'canonical_url',
        'notify_all', 'notify_free', 'notify_subscribers',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'notify_all' => 'boolean',
            'notify_free' => 'boolean',
            'notify_subscribers' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}

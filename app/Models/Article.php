<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasSlug, LogsActivity;

    private const MAX_REVISIONS = 20;

    protected array $activityLogExcept = ['content', 'views_count'];

    protected $fillable = [
        'category_id', 'author_id', 'template_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'status', 'access_level', 'published_at', 'views_count',
        'seo_title', 'seo_description', 'seo_og_image', 'canonical_url',
        'is_imported', 'source_site_id', 'source_url', 'requires_admin_validation',
        'notify_all', 'notify_free', 'notify_subscribers',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_imported' => 'boolean',
            'requires_admin_validation' => 'boolean',
            'notify_all' => 'boolean',
            'notify_free' => 'boolean',
            'notify_subscribers' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if ($article->status === 'published' && blank($article->published_at)) {
                $article->published_at = now();
            }

            $article->access_level ??= 'public';

            if ($article->exists && $article->isDirty(['title', 'excerpt', 'content', 'featured_image', 'access_level'])) {
                $article->recordRevision();
            }
        });

        // views_count changes on every page view via increment(); excluding it here
        // keeps a routine visit from invalidating the homepage/RSS cache.
        static::saved(function (Article $article) {
            if ($article->wasRecentlyCreated || $article->wasChanged(array_diff($article->getFillable(), ['views_count']))) {
                ContentCache::bump();
            }
        });

        static::deleted(fn () => ContentCache::bump());
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(SourceSite::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ArticleRevision::class)->latest();
    }

    private function recordRevision(): void
    {
        $this->revisions()->create([
            'edited_by' => auth()->id(),
            'title' => $this->getOriginal('title'),
            'excerpt' => $this->getOriginal('excerpt'),
            'content' => $this->getOriginal('content'),
            'featured_image' => $this->getOriginal('featured_image'),
            'access_level' => $this->getOriginal('access_level'),
        ]);

        $staleIds = $this->revisions()->skip(self::MAX_REVISIONS)->take(PHP_INT_MAX)->pluck('id');

        if ($staleIds->isNotEmpty()) {
            ArticleRevision::whereIn('id', $staleIds)->delete();
        }
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function isAccessibleBy(?User $user): bool
    {
        if ($user?->hasAnyRole(['admin', 'moderateur'])) {
            return true;
        }

        return match ($this->access_level) {
            'subscribers' => (bool) $user?->hasRole('abonne'),
            'free' => $user !== null,
            default => true,
        };
    }

    public function previewContent(int $words = 60): string
    {
        return Str::words(trim(strip_tags($this->content ?? '')), $words);
    }

    protected function featuredImageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (blank($this->featured_image)) {
                return null;
            }

            return str_starts_with($this->featured_image, 'http')
                ? $this->featured_image
                : asset('storage/'.$this->featured_image);
        });
    }
}

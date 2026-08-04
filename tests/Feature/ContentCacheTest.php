<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Template;
use App\Support\ContentCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_reflects_article_changes_after_cache_invalidation(): void
    {
        Template::create([
            'type' => 'homepage',
            'name' => 'Accueil',
            'is_default' => true,
            'html' => '<div data-block="article-card-grid" data-columns="3" data-rows="2"></div>',
            'css' => '',
        ]);

        $article = Article::create([
            'title' => 'Titre original',
            'slug' => 'titre-original',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
        ]);

        $this->get('/')->assertSee('Titre original');

        $article->update(['title' => 'Titre modifié']);

        $response = $this->get('/');
        $response->assertSee('Titre modifié');
        $response->assertDontSee('Titre original');
    }

    public function test_incrementing_views_does_not_bump_the_cache_version(): void
    {
        $article = Article::create([
            'title' => 'Article vu',
            'slug' => 'article-vu',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
        ]);

        $before = ContentCache::version();

        $article->increment('views_count');

        $this->assertSame($before, ContentCache::version());
    }

    public function test_saving_a_category_bumps_the_cache_version(): void
    {
        $before = ContentCache::version();

        Category::create(['name' => 'Sport', 'slug' => 'sport']);

        $this->assertGreaterThan($before, ContentCache::version());
    }
}

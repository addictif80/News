<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RssFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_feed_lists_published_articles(): void
    {
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Un article publié',
            'slug' => 'un-article-publie',
            'excerpt' => 'Résumé',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Article::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon',
            'status' => 'draft',
        ]);

        $response = $this->get('/rss.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $response->assertSee('Un article publié');
        $response->assertDontSee('Brouillon');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Feed must be valid XML');
        $this->assertCount(1, $xml->channel->item);
    }

    public function test_feed_can_be_filtered_to_specific_categories(): void
    {
        $sport = Category::create(['name' => 'Sport', 'slug' => 'sport']);
        $culture = Category::create(['name' => 'Culture', 'slug' => 'culture']);

        Article::create([
            'category_id' => $sport->id,
            'title' => 'Article sport',
            'slug' => 'article-sport',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Article::create([
            'category_id' => $culture->id,
            'title' => 'Article culture',
            'slug' => 'article-culture',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get('/rss.xml?categories=sport');

        $response->assertSee('Article sport');
        $response->assertDontSee('Article culture');
    }

    public function test_category_specific_feed_route(): void
    {
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Article sport',
            'slug' => 'article-sport',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('rss.category', $category));

        $response->assertOk();
        $response->assertSee('Article sport');
    }
}

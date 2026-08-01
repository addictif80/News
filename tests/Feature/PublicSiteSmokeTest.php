<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_without_template(): void
    {
        Category::create(['name' => 'Économie', 'slug' => 'economie']);

        $this->get('/')->assertOk();
    }

    public function test_homepage_renders_with_a_template_and_blocks(): void
    {
        Template::create([
            'type' => 'homepage',
            'name' => 'Accueil',
            'is_default' => true,
            'html' => '<div data-block="alert-banner"></div><div data-block="article-card-grid" data-columns="3" data-rows="2"></div>',
            'css' => 'body{margin:0;}',
        ]);

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

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Un article publié');
    }

    public function test_article_published_without_explicit_date_still_appears_on_homepage(): void
    {
        Template::create([
            'type' => 'homepage',
            'name' => 'Accueil',
            'is_default' => true,
            'html' => '<div data-block="article-card-grid" data-columns="3" data-rows="2"></div>',
            'css' => '',
        ]);

        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Article sans date choisie',
            'slug' => 'article-sans-date-choisie',
            'excerpt' => 'Résumé',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
        ]);

        $this->assertNotNull($article->fresh()->published_at);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Article sans date choisie');
    }

    public function test_article_page_renders_with_default_template(): void
    {
        Template::create([
            'type' => 'article',
            'name' => 'Article standard',
            'is_default' => true,
            'html' => '<h1>{{title}}</h1><div>{{content}}</div>',
            'css' => '',
        ]);

        $article = Article::create([
            'title' => 'Mon article',
            'slug' => 'mon-article',
            'content' => '<p>Le contenu</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertOk();
        $response->assertSee('Mon article');
        $response->assertSee('Le contenu', false);
    }
}

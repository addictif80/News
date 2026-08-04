<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleLinkCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckArticleLinksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_flags_broken_links_and_images_in_published_articles(): void
    {
        Article::create([
            'title' => 'Article avec liens',
            'slug' => 'article-avec-liens',
            'content' => '<p>Voir <a href="https://ok.example.com/page">ce lien</a> et '
                .'<a href="https://broken.example.com/page">celui-ci</a>.</p>'
                .'<img src="https://ok.example.com/image.jpg">',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Http::fake([
            'ok.example.com/*' => Http::response('', 200),
            'broken.example.com/*' => Http::response('', 404),
        ]);

        $this->artisan('articles:check-links')->assertSuccessful();

        $this->assertSame(3, ArticleLinkCheck::count());
        $this->assertSame(1, ArticleLinkCheck::where('is_broken', true)->count());
        $this->assertDatabaseHas('article_link_checks', [
            'url' => 'https://broken.example.com/page',
            'is_broken' => true,
            'status_code' => 404,
        ]);
        $this->assertDatabaseHas('article_link_checks', [
            'url' => 'https://ok.example.com/image.jpg',
            'type' => 'image',
            'is_broken' => false,
        ]);
    }

    public function test_draft_articles_are_not_checked(): void
    {
        Article::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon-liens',
            'content' => '<a href="https://example.com/x">lien</a>',
            'status' => 'draft',
        ]);

        Http::fake(['example.com/*' => Http::response('', 200)]);

        $this->artisan('articles:check-links')->assertSuccessful();

        $this->assertSame(0, ArticleLinkCheck::count());
    }

    public function test_admin_can_view_the_link_checks_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/article-link-checks');

        $response->assertOk();
    }
}

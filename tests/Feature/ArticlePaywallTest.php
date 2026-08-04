<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArticlePaywallTest extends TestCase
{
    use RefreshDatabase;

    private function makeArticle(string $accessLevel): Article
    {
        return Article::create([
            'title' => 'Article réservé',
            'slug' => 'article-reserve',
            'excerpt' => 'Résumé',
            'content' => '<p>'.str_repeat('phrase complète ', 100).'</p>',
            'status' => 'published',
            'access_level' => $accessLevel,
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_guest_sees_full_content_for_public_article(): void
    {
        $article = $this->makeArticle('public');

        $response = $this->get(route('articles.show', $article));

        $response->assertOk();
        $response->assertDontSee('paywall', false);
    }

    public function test_guest_sees_paywall_for_free_members_article(): void
    {
        $article = $this->makeArticle('free');

        $response = $this->get(route('articles.show', $article));

        $response->assertOk();
        $response->assertSee('réservé aux membres inscrits');
        $response->assertSee("S'inscrire gratuitement", false);
    }

    public function test_free_member_can_read_free_article_but_not_subscriber_article(): void
    {
        Role::firstOrCreate(['name' => 'gratuit', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('gratuit');

        $free = $this->makeArticle('free');

        $this->actingAs($user)
            ->get(route('articles.show', $free))
            ->assertOk()
            ->assertDontSee('réservé aux membres inscrits');

        $subscriberOnly = Article::create([
            'title' => 'Article abonnés',
            'slug' => 'article-abonnes',
            'content' => '<p>Contenu réservé</p>',
            'status' => 'published',
            'access_level' => 'subscribers',
            'published_at' => now()->subHour(),
        ]);

        $this->actingAs($user)
            ->get(route('articles.show', $subscriberOnly))
            ->assertOk()
            ->assertSee('réservé à nos abonnés');
    }

    public function test_subscriber_can_read_subscriber_only_article(): void
    {
        Role::firstOrCreate(['name' => 'abonne', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('abonne');

        $article = $this->makeArticle('subscribers');

        $this->actingAs($user)
            ->get(route('articles.show', $article))
            ->assertOk()
            ->assertDontSee('réservé à nos abonnés')
            ->assertSee('phrase complète');
    }

    public function test_admin_bypasses_paywall(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = $this->makeArticle('subscribers');

        $this->actingAs($admin)
            ->get(route('articles.show', $article))
            ->assertOk()
            ->assertDontSee('réservé à nos abonnés');
    }

    public function test_admin_can_preview_a_free_article_as_a_guest(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = $this->makeArticle('free');

        $this->actingAs($admin)
            ->get(route('articles.show', $article).'?preview_as=guest')
            ->assertOk()
            ->assertSee('réservé aux membres inscrits');
    }

    public function test_admin_preview_as_subscriber_never_shows_the_paywall(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = $this->makeArticle('subscribers');

        $this->actingAs($admin)
            ->get(route('articles.show', $article).'?preview_as=subscriber')
            ->assertOk()
            ->assertDontSee('réservé à nos abonnés');
    }

    public function test_preview_as_is_ignored_for_non_staff_users(): void
    {
        $user = User::factory()->create();
        $article = $this->makeArticle('subscribers');

        $this->actingAs($user)
            ->get(route('articles.show', $article).'?preview_as=subscriber')
            ->assertOk()
            ->assertSee('réservé à nos abonnés');
    }

    public function test_preview_as_does_not_increment_the_view_count(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = $this->makeArticle('public');

        $this->actingAs($admin)->get(route('articles.show', $article).'?preview_as=guest');

        $this->assertSame(0, $article->fresh()->views_count);
    }
}

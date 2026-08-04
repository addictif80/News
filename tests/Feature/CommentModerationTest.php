<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Settings\CommentModerationSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private function makeArticle(): Article
    {
        return Article::create([
            'title' => 'Article commentable',
            'slug' => 'article-commentable',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_a_normal_comment_is_created_pending(): void
    {
        $user = User::factory()->create();
        $article = $this->makeArticle();

        $this->actingAs($user)
            ->post(route('articles.comments.store', $article), ['body' => 'Un commentaire correct'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'status' => 'pending',
        ]);
    }

    public function test_a_banned_user_cannot_post_a_comment(): void
    {
        $user = User::factory()->create(['is_comment_banned' => true]);
        $article = $this->makeArticle();

        $this->actingAs($user)
            ->post(route('articles.comments.store', $article), ['body' => 'Un commentaire'])
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_a_comment_with_a_blocked_word_is_auto_rejected(): void
    {
        app(CommentModerationSettings::class)->fill(['blocked_words' => ['motinterdit']])->save();

        $user = User::factory()->create();
        $article = $this->makeArticle();

        $this->actingAs($user)
            ->post(route('articles.comments.store', $article), ['body' => 'Ceci contient MotInterdit dedans'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'status' => 'rejected',
        ]);
    }
}

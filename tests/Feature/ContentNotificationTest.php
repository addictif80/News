<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Notifications\ContentPublishedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContentNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPushSubscription(string $role): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole($role);
        $user->updatePushSubscription(
            endpoint: 'https://push.example.com/'.$user->id,
            key: 'p256dh-key',
            token: 'auth-token',
        );

        return $user;
    }

    public function test_publishing_with_notify_free_only_notifies_free_users_with_a_subscription(): void
    {
        Notification::fake();

        $free = $this->userWithPushSubscription('gratuit');
        $subscriber = $this->userWithPushSubscription('abonne');

        Article::create([
            'title' => 'Une actu importante',
            'slug' => 'une-actu-importante',
            'excerpt' => 'Résumé',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'notify_free' => true,
        ]);

        Notification::assertSentTo($free, ContentPublishedNotification::class);
        Notification::assertNotSentTo($subscriber, ContentPublishedNotification::class);
    }

    public function test_notify_flags_reset_after_sending(): void
    {
        Notification::fake();

        $this->userWithPushSubscription('gratuit');

        $article = Article::create([
            'title' => 'Une autre actu',
            'slug' => 'une-autre-actu',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'notify_all' => true,
        ]);

        $this->assertFalse($article->fresh()->notify_all);
    }

    public function test_draft_articles_never_trigger_a_notification(): void
    {
        Notification::fake();

        $free = $this->userWithPushSubscription('gratuit');

        Article::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
            'notify_all' => true,
        ]);

        Notification::assertNothingSent();
    }
}

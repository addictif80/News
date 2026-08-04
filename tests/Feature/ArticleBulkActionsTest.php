<?php

namespace Tests\Feature;

use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArticleBulkActionsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_can_publish_several_pending_articles_at_once(): void
    {
        $admin = $this->admin();

        $first = Article::create([
            'title' => 'Article importé 1',
            'slug' => 'article-importe-1',
            'content' => '<p>Contenu</p>',
            'status' => 'pending_review',
            'is_imported' => true,
        ]);

        $second = Article::create([
            'title' => 'Article importé 2',
            'slug' => 'article-importe-2',
            'content' => '<p>Contenu</p>',
            'status' => 'pending_review',
            'is_imported' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('publishSelection', [$first, $second]);

        $this->assertSame('published', $first->fresh()->status);
        $this->assertSame('published', $second->fresh()->status);
        $this->assertNotNull($first->fresh()->published_at);
        $this->assertNotNull($second->fresh()->published_at);
    }

    public function test_admin_can_delete_several_articles_at_once(): void
    {
        $admin = $this->admin();

        $first = Article::create([
            'title' => 'À supprimer 1',
            'slug' => 'a-supprimer-1',
            'content' => '<p>Contenu</p>',
            'status' => 'pending_review',
        ]);

        $second = Article::create([
            'title' => 'À supprimer 2',
            'slug' => 'a-supprimer-2',
            'content' => '<p>Contenu</p>',
            'status' => 'pending_review',
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('delete', [$first, $second]);

        $this->assertDatabaseCount('articles', 0);
    }
}

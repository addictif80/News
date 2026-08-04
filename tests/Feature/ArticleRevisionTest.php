<?php

namespace Tests\Feature;

use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\RelationManagers\RevisionsRelationManager;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArticleRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_content_creates_a_revision_of_the_previous_state(): void
    {
        $article = Article::create([
            'title' => 'Titre v1',
            'slug' => 'titre-v1',
            'content' => '<p>Contenu v1</p>',
            'status' => 'draft',
        ]);

        $article->update(['title' => 'Titre v2', 'content' => '<p>Contenu v2</p>']);

        $this->assertCount(1, $article->revisions);
        $this->assertSame('Titre v1', $article->revisions->first()->title);
        $this->assertSame('<p>Contenu v1</p>', $article->revisions->first()->content);
    }

    public function test_updating_unrelated_fields_does_not_create_a_revision(): void
    {
        $article = Article::create([
            'title' => 'Titre',
            'slug' => 'titre',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
        ]);

        $article->update(['views_count' => 5]);

        $this->assertCount(0, $article->fresh()->revisions);
    }

    public function test_only_the_last_twenty_revisions_are_kept(): void
    {
        $article = Article::create([
            'title' => 'Titre 0',
            'slug' => 'titre-pruning',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
        ]);

        for ($i = 1; $i <= 25; $i++) {
            $article->update(['title' => "Titre {$i}"]);
        }

        $this->assertSame(20, $article->revisions()->count());
    }

    public function test_admin_can_restore_a_previous_revision(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = Article::create([
            'title' => 'Titre original',
            'slug' => 'titre-original',
            'content' => '<p>Contenu original</p>',
            'status' => 'draft',
        ]);

        $article->update(['title' => 'Titre modifié', 'content' => '<p>Contenu modifié</p>']);

        $revision = $article->revisions->first();

        $this->actingAs($admin);

        Livewire::test(RevisionsRelationManager::class, [
            'ownerRecord' => $article,
            'pageClass' => EditArticle::class,
        ])->callTableAction('restore', $revision);

        $this->assertSame('Titre original', $article->fresh()->title);
    }
}

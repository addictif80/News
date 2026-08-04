<?php

namespace Tests\Feature;

use App\Models\ActivityLogEntry;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_article_is_logged(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $article = Article::create([
            'title' => 'Un article',
            'slug' => 'un-article',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
        ]);

        $entry = ActivityLogEntry::where('subject_type', Article::class)
            ->where('subject_id', $article->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame($admin->id, $entry->causer_id);
        $this->assertArrayNotHasKey('content', $entry->changes ?? []);
    }

    public function test_updating_an_article_logs_only_the_changed_fields(): void
    {
        $article = Article::create([
            'title' => 'Titre initial',
            'slug' => 'titre-initial',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
        ]);

        ActivityLogEntry::query()->delete();

        $article->update(['title' => 'Titre modifié']);

        $entry = ActivityLogEntry::where('subject_id', $article->id)->where('event', 'updated')->first();

        $this->assertNotNull($entry);
        $this->assertArrayHasKey('title', $entry->changes);
        $this->assertSame('Titre initial', $entry->changes['title']['old']);
        $this->assertSame('Titre modifié', $entry->changes['title']['new']);
    }

    public function test_incrementing_views_does_not_create_a_log_entry(): void
    {
        $article = Article::create([
            'title' => 'Vu',
            'slug' => 'vu',
            'content' => '<p>Contenu</p>',
            'status' => 'draft',
        ]);

        ActivityLogEntry::query()->delete();

        $article->increment('views_count');

        $this->assertSame(0, ActivityLogEntry::count());
    }

    public function test_deleting_a_category_is_logged(): void
    {
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);
        ActivityLogEntry::query()->delete();

        $category->delete();

        $this->assertDatabaseHas('activity_log_entries', [
            'subject_type' => Category::class,
            'event' => 'deleted',
        ]);
    }

    public function test_admin_can_view_the_activity_log_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/activity-log-entries');

        $response->assertOk();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_load_the_dashboard_with_stats_and_warnings(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Article::create([
            'title' => 'Article de la semaine',
            'slug' => 'article-de-la-semaine',
            'content' => '<p>Contenu</p>',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Articles publiés cette semaine');
        $response->assertSee('Configuration à finaliser');
    }
}

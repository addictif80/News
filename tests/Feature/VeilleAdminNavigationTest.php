<?php

namespace Tests\Feature;

use App\Filament\Resources\SourceSites\Pages\CreateSourceSite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VeilleAdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reach_the_source_sites_pages(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/admin/source-sites')->assertOk();
        $this->actingAs($admin)->get('/admin/source-sites/create')->assertOk();
    }

    public function test_admin_can_create_a_source_site(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(CreateSourceSite::class)
            ->fillForm([
                'name' => 'Le Monde',
                'base_url' => 'https://www.lemonde.fr',
                'rss_feed_url' => 'https://www.lemonde.fr/rss/une.xml',
                'is_active' => true,
                'used_for_watch' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('source_sites', ['name' => 'Le Monde']);
    }

    public function test_admin_can_reach_the_keywords_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/admin/keywords')->assertOk();
    }
}

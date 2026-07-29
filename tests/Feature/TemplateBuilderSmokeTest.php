<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TemplateBuilderSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_template_builder(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $template = Template::create([
            'type' => 'article',
            'name' => 'Article standard',
            'is_default' => true,
        ]);

        $response = $this->actingAs($admin)->get("/admin/templates/{$template->id}/builder");

        $response->assertOk();
        $response->assertSee('gjs-app', false);
    }
}

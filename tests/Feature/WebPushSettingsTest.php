<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Minishlink\WebPush\VAPID;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebPushSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_web_push_settings_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/manage-web-push-settings');

        $response->assertOk();
        $response->assertSee('Notifications push');
    }

    public function test_it_can_generate_a_valid_vapid_keypair(): void
    {
        $keys = VAPID::createVapidKeys();

        $this->assertArrayHasKey('publicKey', $keys);
        $this->assertArrayHasKey('privateKey', $keys);
        $this->assertNotEmpty($keys['publicKey']);
        $this->assertNotEmpty($keys['privateKey']);
    }
}

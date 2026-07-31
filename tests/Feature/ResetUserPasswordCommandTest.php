<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetUserPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resets_a_users_password(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $this->artisan('user:reset-password', [
            'email' => 'admin@example.com',
            '--password' => 'nouveau-mot-de-passe',
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('nouveau-mot-de-passe', $user->fresh()->password));
    }

    public function test_it_fails_gracefully_for_an_unknown_email(): void
    {
        $this->artisan('user:reset-password', ['email' => 'inconnu@example.com'])
            ->assertFailed();
    }
}

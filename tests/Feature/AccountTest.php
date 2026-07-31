<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private function member(): User
    {
        Role::firstOrCreate(['name' => 'gratuit', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('gratuit');

        return $user;
    }

    public function test_account_page_loads_for_an_authenticated_member(): void
    {
        $response = $this->actingAs($this->member())->get('/compte');

        $response->assertOk();
        $response->assertSee('Mon compte');
    }

    public function test_user_can_update_profile_and_avatar(): void
    {
        Storage::fake('public');
        $user = $this->member();

        $response = $this->actingAs($user)->put('/compte', [
            'name' => 'Nouveau nom',
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertSame('Nouveau nom', $user->name);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_set_favorite_categories(): void
    {
        $user = $this->member();
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);

        $this->actingAs($user)->post('/compte/interets', [
            'category_ids' => [$category->id],
        ])->assertRedirect();

        $this->assertTrue($user->favoriteCategories()->where('categories.id', $category->id)->exists());
    }

    public function test_user_can_export_their_data(): void
    {
        $user = $this->member();

        $response = $this->actingAs($user)->get('/compte/export');

        $response->assertOk();
        $response->assertJsonStructure(['profile', 'favorite_categories', 'comments']);
    }

    public function test_user_can_delete_their_account_which_anonymizes_it(): void
    {
        $user = $this->member();
        $user->password = 'correct-password';
        $user->save();

        $response = $this->actingAs($user)->delete('/compte', [
            'password' => 'correct-password',
        ]);

        $response->assertRedirect('/');
        $user->refresh();
        $this->assertSame('Utilisateur supprimé', $user->name);
        $this->assertNotNull($user->deleted_data_at);
        $this->assertEmpty($user->getRoleNames());
    }

    public function test_anonymized_account_cannot_log_back_in(): void
    {
        $user = $this->member();
        $user->update([
            'deleted_data_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}

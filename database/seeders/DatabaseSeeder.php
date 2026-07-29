<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RoleSeeder::class, PermissionSeeder::class]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            User::factory()->raw(['name' => 'Administrateur'])
        );
        $admin->assignRole('admin');
    }
}

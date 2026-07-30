<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RoleSeeder::class, PermissionSeeder::class]);

        if (User::where('email', 'admin@example.com')->exists()) {
            return;
        }

        $password = Str::password(16);

        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@example.com',
            'password' => Hash::make($password),
        ]);
        $admin->assignRole('admin');

        $this->command?->warn("Compte admin créé : admin@example.com / {$password}");
        $this->command?->warn('Connectez-vous et changez ce mot de passe immédiatement.');
    }
}

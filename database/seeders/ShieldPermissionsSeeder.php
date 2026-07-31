<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ShieldPermissionsSeeder extends Seeder
{
    /**
     * Recreate the Filament Shield permission rows for every panel
     * resource/page/widget. Policies themselves are plain PHP classes
     * already committed to the repo — only the permission *records* need
     * regenerating, since they live in the database rather than in code.
     * Safe to run repeatedly (Shield uses firstOrCreate internally).
     */
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'permissions',
            '--no-interaction' => true,
        ]);
    }
}

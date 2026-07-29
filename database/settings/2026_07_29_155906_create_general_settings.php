<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', "Mon Site d'Actualités");
        $this->migrator->add('general.site_logo', null);
    }
};

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('veille.is_enabled', false);
        $this->migrator->add('veille.polling_interval_minutes', 60);
        $this->migrator->add('veille.requires_admin_validation', true);
    }
};

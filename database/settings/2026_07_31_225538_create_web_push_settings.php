<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('webpush.is_enabled', false);
        $this->migrator->add('webpush.subject', config('app.url'));
        $this->migrator->add('webpush.public_key', '');
        $this->migrator->add('webpush.private_key', '');
    }
};

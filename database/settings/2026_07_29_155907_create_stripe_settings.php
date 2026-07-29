<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('stripe.public_key', '');
        $this->migrator->add('stripe.secret_key', '');
        $this->migrator->add('stripe.webhook_secret', '');
    }
};

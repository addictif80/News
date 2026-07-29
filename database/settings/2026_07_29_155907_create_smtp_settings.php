<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('smtp.host', '');
        $this->migrator->add('smtp.port', 587);
        $this->migrator->add('smtp.username', '');
        $this->migrator->add('smtp.password', '');
        $this->migrator->add('smtp.encryption', 'tls');
        $this->migrator->add('smtp.from_address', '');
        $this->migrator->add('smtp.from_name', '');
    }
};

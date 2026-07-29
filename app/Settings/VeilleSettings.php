<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class VeilleSettings extends Settings
{
    public bool $is_enabled;

    public int $polling_interval_minutes;

    public bool $requires_admin_validation;

    public static function group(): string
    {
        return 'veille';
    }
}

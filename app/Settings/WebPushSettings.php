<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WebPushSettings extends Settings
{
    public bool $is_enabled;

    public string $subject;

    public string $public_key;

    public string $private_key;

    public static function group(): string
    {
        return 'webpush';
    }
}

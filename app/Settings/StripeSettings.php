<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StripeSettings extends Settings
{
    public string $public_key;

    public string $secret_key;

    public string $webhook_secret;

    public static function group(): string
    {
        return 'stripe';
    }
}

<?php

namespace App\Providers;

use App\Settings\SmtpSettings;
use App\Settings\StripeSettings;
use App\Settings\WebPushSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDynamicMailer();
        $this->configureDynamicStripe();
        $this->configureDynamicWebPush();
    }

    private function configureDynamicMailer(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        try {
            $settings = app(SmtpSettings::class);

            if (blank($settings->host)) {
                return;
            }

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $settings->host);
            Config::set('mail.mailers.smtp.port', $settings->port);
            Config::set('mail.mailers.smtp.username', $settings->username);
            Config::set('mail.mailers.smtp.password', $settings->password);
            Config::set('mail.mailers.smtp.encryption', $settings->encryption === 'none' ? null : $settings->encryption);
            Config::set('mail.from.address', $settings->from_address);
            Config::set('mail.from.name', $settings->from_name);
        } catch (\Throwable) {
            // Settings not migrated yet (e.g. mid-`artisan migrate` run) — ignore.
        }
    }

    private function configureDynamicStripe(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        try {
            $settings = app(StripeSettings::class);

            if (blank($settings->secret_key)) {
                return;
            }

            Config::set('cashier.key', $settings->public_key);
            Config::set('cashier.secret', $settings->secret_key);
            Config::set('cashier.webhook.secret', $settings->webhook_secret);
        } catch (\Throwable) {
            // Settings not migrated yet (e.g. mid-`artisan migrate` run) — ignore.
        }
    }

    private function configureDynamicWebPush(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        try {
            $settings = app(WebPushSettings::class);

            if (blank($settings->public_key)) {
                return;
            }

            Config::set('webpush.vapid.subject', $settings->subject);
            Config::set('webpush.vapid.public_key', $settings->public_key);
            Config::set('webpush.vapid.private_key', $settings->private_key);
        } catch (\Throwable) {
            // Settings not migrated yet (e.g. mid-`artisan migrate` run) — ignore.
        }
    }
}

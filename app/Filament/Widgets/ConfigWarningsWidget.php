<?php

namespace App\Filament\Widgets;

use App\Settings\SmtpSettings;
use App\Settings\StripeSettings;
use App\Settings\WebPushSettings;
use Filament\Widgets\Widget;

class ConfigWarningsWidget extends Widget
{
    protected string $view = 'filament.widgets.config-warnings';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function getWarnings(): array
    {
        $warnings = [];

        $smtp = app(SmtpSettings::class);
        if (blank($smtp->host) || blank($smtp->username)) {
            $warnings[] = [
                'label' => "Le serveur SMTP n'est pas configuré",
                'url' => route('filament.admin.pages.manage-smtp-settings'),
            ];
        }

        $stripe = app(StripeSettings::class);
        if (blank($stripe->secret_key) || blank($stripe->public_key)) {
            $warnings[] = [
                'label' => "Stripe n'est pas configuré — les abonnements payants sont indisponibles",
                'url' => route('filament.admin.pages.manage-stripe-settings'),
            ];
        }

        $webPush = app(WebPushSettings::class);
        if ($webPush->is_enabled && (blank($webPush->public_key) || blank($webPush->private_key))) {
            $warnings[] = [
                'label' => 'Les notifications push sont activées mais les clés VAPID sont manquantes',
                'url' => route('filament.admin.pages.manage-web-push-settings'),
            ];
        }

        return $warnings;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}

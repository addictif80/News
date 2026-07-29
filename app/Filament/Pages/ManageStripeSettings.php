<?php

namespace App\Filament\Pages;

use App\Settings\StripeSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageStripeSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static UnitEnum|string|null $navigationGroup = 'Réglages';

    protected static ?string $title = 'Stripe';

    protected static string $settings = StripeSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('public_key')->label('Clé publique')->required(),
                TextInput::make('secret_key')->label('Clé secrète')->password()->revealable()->required(),
                TextInput::make('webhook_secret')->label('Secret webhook')->password()->revealable()->required(),
            ]);
    }
}

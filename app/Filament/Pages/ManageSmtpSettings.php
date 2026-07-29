<?php

namespace App\Filament\Pages;

use App\Settings\SmtpSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSmtpSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static UnitEnum|string|null $navigationGroup = 'Réglages';

    protected static ?string $title = 'Serveur SMTP';

    protected static string $settings = SmtpSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('host')->label('Hôte SMTP')->required(),
                TextInput::make('port')->label('Port')->numeric()->required(),
                TextInput::make('username')->label("Nom d'utilisateur")->required(),
                TextInput::make('password')->label('Mot de passe')->password()->revealable()->required(),
                Select::make('encryption')
                    ->label('Chiffrement')
                    ->options(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'Aucun'])
                    ->required(),
                TextInput::make('from_address')->label('Adresse expéditeur')->email()->required(),
                TextInput::make('from_name')->label('Nom expéditeur')->required(),
            ]);
    }
}

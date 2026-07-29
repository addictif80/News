<?php

namespace App\Filament\Pages;

use App\Settings\VeilleSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageVeilleSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static UnitEnum|string|null $navigationGroup = 'Réglages';

    protected static ?string $title = 'Veille informationnelle';

    protected static string $settings = VeilleSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'moderateur']) ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_enabled')->label('Activer la veille automatisée'),
                TextInput::make('polling_interval_minutes')
                    ->label('Intervalle de vérification (minutes)')
                    ->numeric()
                    ->minValue(15)
                    ->required(),
                Toggle::make('requires_admin_validation')
                    ->label('Validation obligatoire par un admin avant publication'),
            ]);
    }
}

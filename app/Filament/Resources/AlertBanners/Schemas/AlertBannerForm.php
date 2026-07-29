<?php

namespace App\Filament\Resources\AlertBanners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AlertBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('message')
                    ->label('Message')
                    ->required(),
                TextInput::make('link_url')
                    ->label('Lien')
                    ->url(),
                Select::make('style')
                    ->label('Style')
                    ->options([
                        'info' => 'Information',
                        'success' => 'Succès',
                        'warning' => 'Avertissement',
                        'danger' => 'Urgent',
                    ])
                    ->default('info')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Actif'),
                DateTimePicker::make('starts_at')->label('Début'),
                DateTimePicker::make('ends_at')->label('Fin'),
            ]);
    }
}

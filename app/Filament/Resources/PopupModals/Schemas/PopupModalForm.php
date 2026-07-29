<?php

namespace App\Filament\Resources\PopupModals\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PopupModalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                RichEditor::make('content')
                    ->label('Contenu')
                    ->required()
                    ->columnSpanFull(),
                Select::make('trigger_type')
                    ->label('Déclencheur')
                    ->options([
                        'load' => 'Au chargement',
                        'delay' => 'Après un délai',
                        'scroll' => 'Au défilement',
                        'exit_intent' => 'Intention de sortie',
                    ])
                    ->default('delay')
                    ->live()
                    ->required(),
                TextInput::make('trigger_value')
                    ->label('Valeur (secondes ou % de scroll)')
                    ->numeric()
                    ->visible(fn (callable $get) => in_array($get('trigger_type'), ['delay', 'scroll'])),
                TagsInput::make('target_pages')
                    ->label('Pages ciblées (slugs, vide = toutes)')
                    ->separator(','),
                TextInput::make('display_frequency_days')
                    ->label("Fréquence d'affichage (jours)")
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('is_active')
                    ->label('Actif'),
                DateTimePicker::make('starts_at')->label('Début'),
                DateTimePicker::make('ends_at')->label('Fin'),
            ]);
    }
}

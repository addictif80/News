<?php

namespace App\Filament\Resources\Templates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom du template')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'article' => 'Article',
                        'page' => 'Page',
                        'homepage' => "Page d'accueil",
                    ])
                    ->required(),
                Toggle::make('is_default')
                    ->label('Template par défaut pour ce type'),
            ]);
    }
}

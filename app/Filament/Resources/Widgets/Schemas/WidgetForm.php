<?php

namespace App\Filament\Resources\Widgets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WidgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('token')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('articles_count')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('theme')
                    ->required()
                    ->default('light'),
            ]);
    }
}

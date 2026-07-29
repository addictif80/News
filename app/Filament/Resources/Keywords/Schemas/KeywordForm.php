<?php

namespace App\Filament\Resources\Keywords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KeywordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('term')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}

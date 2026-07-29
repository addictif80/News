<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('avatar'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('social_links')
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuthorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('social_links')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

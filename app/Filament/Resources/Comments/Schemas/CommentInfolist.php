<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('article.title')
                    ->label('Article'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('parent.id')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('body')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Widgets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WidgetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('token'),
                TextEntry::make('category.name')
                    ->label('Category')
                    ->placeholder('-'),
                TextEntry::make('articles_count')
                    ->numeric(),
                TextEntry::make('theme'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

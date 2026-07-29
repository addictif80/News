<?php

namespace App\Filament\Resources\PopupModals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PopupModalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('content')
                    ->columnSpanFull(),
                TextEntry::make('trigger_type'),
                TextEntry::make('trigger_value')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('target_pages')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('display_frequency_days')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('starts_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('ends_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\AlertBanners\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AlertBannerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('message'),
                TextEntry::make('link_url')
                    ->placeholder('-'),
                TextEntry::make('style'),
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

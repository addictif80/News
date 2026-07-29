<?php

namespace App\Filament\Resources\SourceSites\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SourceSiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('base_url'),
                TextEntry::make('rss_feed_url')
                    ->placeholder('-'),
                TextEntry::make('logo')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('used_for_watch')
                    ->boolean(),
                TextEntry::make('last_polled_at')
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

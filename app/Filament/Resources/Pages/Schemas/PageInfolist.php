<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('template.name')
                    ->label('Template')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                ImageEntry::make('featured_image')
                    ->placeholder('-'),
                TextEntry::make('content')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('seo_title')
                    ->placeholder('-'),
                TextEntry::make('seo_description')
                    ->placeholder('-'),
                ImageEntry::make('seo_og_image')
                    ->placeholder('-'),
                TextEntry::make('canonical_url')
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

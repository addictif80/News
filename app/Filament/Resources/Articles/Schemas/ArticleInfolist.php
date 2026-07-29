<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.name')
                    ->label('Category')
                    ->placeholder('-'),
                TextEntry::make('author.name')
                    ->label('Author')
                    ->placeholder('-'),
                TextEntry::make('template.name')
                    ->label('Template')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('excerpt')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('featured_image')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('views_count')
                    ->numeric(),
                TextEntry::make('seo_title')
                    ->placeholder('-'),
                TextEntry::make('seo_description')
                    ->placeholder('-'),
                ImageEntry::make('seo_og_image')
                    ->placeholder('-'),
                TextEntry::make('canonical_url')
                    ->placeholder('-'),
                IconEntry::make('is_imported')
                    ->boolean(),
                TextEntry::make('sourceSite.name')
                    ->label('Source site')
                    ->placeholder('-'),
                TextEntry::make('source_url')
                    ->placeholder('-'),
                IconEntry::make('requires_admin_validation')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

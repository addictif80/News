<?php

namespace App\Filament\Resources\NewsletterCampaigns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NewsletterCampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject'),
                TextEntry::make('body_html')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('scheduled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('sent_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('recipients_count')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

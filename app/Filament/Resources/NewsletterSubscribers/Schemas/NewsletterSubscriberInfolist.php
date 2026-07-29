<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NewsletterSubscriberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                IconEntry::make('is_confirmed')
                    ->boolean(),
                TextEntry::make('subscribed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('unsubscribed_at')
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

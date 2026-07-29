<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Toggle::make('is_confirmed')
                    ->required(),
                DateTimePicker::make('subscribed_at'),
                DateTimePicker::make('unsubscribed_at'),
            ]);
    }
}

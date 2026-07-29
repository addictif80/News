<?php

namespace App\Filament\Resources\SourceSites\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SourceSiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('base_url')
                    ->url()
                    ->required(),
                TextInput::make('rss_feed_url')
                    ->url(),
                TextInput::make('logo'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('used_for_watch')
                    ->required(),
                DateTimePicker::make('last_polled_at'),
            ]);
    }
}

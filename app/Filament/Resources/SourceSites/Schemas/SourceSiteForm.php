<?php

namespace App\Filament\Resources\SourceSites\Schemas;

use Filament\Forms\Components\FileUpload;
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
                    ->label('Nom')
                    ->required(),
                TextInput::make('base_url')
                    ->label('URL de base')
                    ->url()
                    ->required(),
                TextInput::make('rss_feed_url')
                    ->label('Flux RSS (pour la veille)')
                    ->url(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('source-sites'),
                Toggle::make('is_active')
                    ->label('Actif'),
                Toggle::make('used_for_watch')
                    ->label('Utilisé pour la veille automatisée'),
            ]);
    }
}

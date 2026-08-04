<?php

namespace App\Filament\Resources\SourceSites\Schemas;

use App\Services\FeedDiscoveryService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                    ->url()
                    ->helperText("Utilise le bouton pour tenter une détection automatique depuis l'URL de base.")
                    ->suffixAction(
                        Action::make('discoverFeed')
                            ->label('Détecter')
                            ->icon(Heroicon::OutlinedMagnifyingGlass)
                            ->action(function (callable $get, callable $set, FeedDiscoveryService $discovery) {
                                $baseUrl = $get('base_url');

                                if (blank($baseUrl)) {
                                    Notification::make()
                                        ->title("Renseigne d'abord l'URL de base")
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $feedUrl = $discovery->discover($baseUrl);

                                if ($feedUrl === null) {
                                    Notification::make()
                                        ->title('Aucun flux RSS détecté automatiquement')
                                        ->body("Le site n'annonce pas de flux dans sa page d'accueil — renseigne-le manuellement si tu le connais.")
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $set('rss_feed_url', $feedUrl);

                                Notification::make()
                                    ->title('Flux RSS détecté')
                                    ->success()
                                    ->send();
                            }),
                    ),
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

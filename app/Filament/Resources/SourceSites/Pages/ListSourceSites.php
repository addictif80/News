<?php

namespace App\Filament\Resources\SourceSites\Pages;

use App\Filament\Resources\SourceSites\SourceSiteResource;
use App\Filament\Resources\SourceSites\Tables\SourceSitesTable;
use App\Models\SourceSite;
use App\Services\VeilleService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSourceSites extends ListRecords
{
    protected static string $resource = SourceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pollAll')
                ->label('Lancer la veille (tous les sites actifs)')
                ->icon(Heroicon::OutlinedArrowPath)
                ->requiresConfirmation()
                ->action(function (VeilleService $veille) {
                    if (SourceSitesTable::warnIfNoActiveKeyword()) {
                        return;
                    }

                    $sites = SourceSite::query()
                        ->where('is_active', true)
                        ->where('used_for_watch', true)
                        ->whereNotNull('rss_feed_url')
                        ->get();

                    SourceSitesTable::notifyResult($veille->pollNow($sites));
                }),
            CreateAction::make(),
        ];
    }
}

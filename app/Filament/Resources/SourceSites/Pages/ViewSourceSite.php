<?php

namespace App\Filament\Resources\SourceSites\Pages;

use App\Filament\Resources\SourceSites\SourceSiteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSourceSite extends ViewRecord
{
    protected static string $resource = SourceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

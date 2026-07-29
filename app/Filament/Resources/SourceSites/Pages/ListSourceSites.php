<?php

namespace App\Filament\Resources\SourceSites\Pages;

use App\Filament\Resources\SourceSites\SourceSiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSourceSites extends ListRecords
{
    protected static string $resource = SourceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

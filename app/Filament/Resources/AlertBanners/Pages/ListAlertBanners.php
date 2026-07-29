<?php

namespace App\Filament\Resources\AlertBanners\Pages;

use App\Filament\Resources\AlertBanners\AlertBannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlertBanners extends ListRecords
{
    protected static string $resource = AlertBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

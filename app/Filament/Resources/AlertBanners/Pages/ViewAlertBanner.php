<?php

namespace App\Filament\Resources\AlertBanners\Pages;

use App\Filament\Resources\AlertBanners\AlertBannerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAlertBanner extends ViewRecord
{
    protected static string $resource = AlertBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

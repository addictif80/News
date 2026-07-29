<?php

namespace App\Filament\Resources\AlertBanners\Pages;

use App\Filament\Resources\AlertBanners\AlertBannerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAlertBanner extends EditRecord
{
    protected static string $resource = AlertBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\SourceSites\Pages;

use App\Filament\Resources\SourceSites\SourceSiteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSourceSite extends EditRecord
{
    protected static string $resource = SourceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

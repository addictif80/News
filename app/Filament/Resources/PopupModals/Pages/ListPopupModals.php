<?php

namespace App\Filament\Resources\PopupModals\Pages;

use App\Filament\Resources\PopupModals\PopupModalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPopupModals extends ListRecords
{
    protected static string $resource = PopupModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

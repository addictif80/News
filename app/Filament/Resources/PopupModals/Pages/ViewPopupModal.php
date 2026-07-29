<?php

namespace App\Filament\Resources\PopupModals\Pages;

use App\Filament\Resources\PopupModals\PopupModalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPopupModal extends ViewRecord
{
    protected static string $resource = PopupModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

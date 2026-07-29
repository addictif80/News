<?php

namespace App\Filament\Resources\PopupModals\Pages;

use App\Filament\Resources\PopupModals\PopupModalResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPopupModal extends EditRecord
{
    protected static string $resource = PopupModalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

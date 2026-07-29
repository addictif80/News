<?php

namespace App\Filament\Resources\Widgets\Pages;

use App\Filament\Resources\Widgets\WidgetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWidget extends ViewRecord
{
    protected static string $resource = WidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

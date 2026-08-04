<?php

namespace App\Filament\Resources\ActivityLogEntries\Pages;

use App\Filament\Resources\ActivityLogEntries\ActivityLogEntryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageActivityLogEntries extends ManageRecords
{
    protected static string $resource = ActivityLogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

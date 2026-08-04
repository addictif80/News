<?php

namespace App\Filament\Resources\ImportLogEntries\Pages;

use App\Filament\Resources\ImportLogEntries\ImportLogEntryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageImportLogEntries extends ManageRecords
{
    protected static string $resource = ImportLogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

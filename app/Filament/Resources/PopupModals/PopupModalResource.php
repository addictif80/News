<?php

namespace App\Filament\Resources\PopupModals;

use App\Filament\Resources\PopupModals\Pages\CreatePopupModal;
use App\Filament\Resources\PopupModals\Pages\EditPopupModal;
use App\Filament\Resources\PopupModals\Pages\ListPopupModals;
use App\Filament\Resources\PopupModals\Pages\ViewPopupModal;
use App\Filament\Resources\PopupModals\Schemas\PopupModalForm;
use App\Filament\Resources\PopupModals\Schemas\PopupModalInfolist;
use App\Filament\Resources\PopupModals\Tables\PopupModalsTable;
use App\Models\PopupModal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PopupModalResource extends Resource
{
    protected static ?string $model = PopupModal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PopupModalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PopupModalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PopupModalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPopupModals::route('/'),
            'create' => CreatePopupModal::route('/create'),
            'view' => ViewPopupModal::route('/{record}'),
            'edit' => EditPopupModal::route('/{record}/edit'),
        ];
    }
}

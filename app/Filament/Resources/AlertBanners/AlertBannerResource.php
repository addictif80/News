<?php

namespace App\Filament\Resources\AlertBanners;

use App\Filament\Resources\AlertBanners\Pages\CreateAlertBanner;
use App\Filament\Resources\AlertBanners\Pages\EditAlertBanner;
use App\Filament\Resources\AlertBanners\Pages\ListAlertBanners;
use App\Filament\Resources\AlertBanners\Pages\ViewAlertBanner;
use App\Filament\Resources\AlertBanners\Schemas\AlertBannerForm;
use App\Filament\Resources\AlertBanners\Schemas\AlertBannerInfolist;
use App\Filament\Resources\AlertBanners\Tables\AlertBannersTable;
use App\Models\AlertBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AlertBannerResource extends Resource
{
    protected static ?string $model = AlertBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AlertBannerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlertBannerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlertBannersTable::configure($table);
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
            'index' => ListAlertBanners::route('/'),
            'create' => CreateAlertBanner::route('/create'),
            'view' => ViewAlertBanner::route('/{record}'),
            'edit' => EditAlertBanner::route('/{record}/edit'),
        ];
    }
}

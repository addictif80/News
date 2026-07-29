<?php

namespace App\Filament\Resources\SourceSites;

use App\Filament\Resources\SourceSites\Pages\CreateSourceSite;
use App\Filament\Resources\SourceSites\Pages\EditSourceSite;
use App\Filament\Resources\SourceSites\Pages\ListSourceSites;
use App\Filament\Resources\SourceSites\Pages\ViewSourceSite;
use App\Filament\Resources\SourceSites\Schemas\SourceSiteForm;
use App\Filament\Resources\SourceSites\Schemas\SourceSiteInfolist;
use App\Filament\Resources\SourceSites\Tables\SourceSitesTable;
use App\Models\SourceSite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SourceSiteResource extends Resource
{
    protected static ?string $model = SourceSite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SourceSiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SourceSiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SourceSitesTable::configure($table);
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
            'index' => ListSourceSites::route('/'),
            'create' => CreateSourceSite::route('/create'),
            'view' => ViewSourceSite::route('/{record}'),
            'edit' => EditSourceSite::route('/{record}/edit'),
        ];
    }
}

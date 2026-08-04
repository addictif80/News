<?php

namespace App\Filament\Resources\ArticleLinkChecks;

use App\Filament\Resources\ArticleLinkChecks\Pages\ManageArticleLinkChecks;
use App\Models\ArticleLinkCheck;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticleLinkCheckResource extends Resource
{
    protected static ?string $model = ArticleLinkCheck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Liens cassés';

    protected static ?string $modelLabel = 'lien vérifié';

    protected static ?string $pluralModelLabel = 'Vérification des liens';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('is_broken', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_broken')
                    ->label('Cassé')
                    ->boolean(),
                TextColumn::make('article.title')
                    ->label('Article')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => $state === 'image' ? 'Image' : 'Lien'),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('status_code')
                    ->label('Code HTTP')
                    ->placeholder('—'),
                TextColumn::make('checked_at')
                    ->label('Vérifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_broken')
                    ->label('Cassé uniquement')
                    ->default(true)
                    ->queries(
                        true: fn ($query) => $query->where('is_broken', true),
                        false: fn ($query) => $query->where('is_broken', false),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('checked_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageArticleLinkChecks::route('/'),
        ];
    }
}

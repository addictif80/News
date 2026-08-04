<?php

namespace App\Filament\Resources\ImportLogEntries;

use App\Filament\Resources\ImportLogEntries\Pages\ManageImportLogEntries;
use App\Models\ImportLogEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ImportLogEntryResource extends Resource
{
    protected static ?string $model = ImportLogEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Journal de veille';

    protected static ?string $modelLabel = "entrée du journal d'import";

    protected static ?string $pluralModelLabel = "Journal d'import";

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
                TextColumn::make('created_at')
                    ->label('Quand')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('outcome')
                    ->label('Résultat')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'imported' => 'Importé',
                        'duplicate' => 'Doublon',
                        'error' => 'Erreur',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'imported' => 'success',
                        'duplicate' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('sourceSite.name')
                    ->label('Site source')
                    ->searchable(),
                TextColumn::make('keyword')
                    ->label('Mot-clé')
                    ->placeholder('—'),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('article.title')
                    ->label('Article')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('message')
                    ->label('Détail')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('outcome')
                    ->label('Résultat')
                    ->options([
                        'imported' => 'Importé',
                        'duplicate' => 'Doublon',
                        'error' => 'Erreur',
                    ]),
                SelectFilter::make('source_site_id')
                    ->label('Site source')
                    ->relationship('sourceSite', 'name'),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageImportLogEntries::route('/'),
        ];
    }
}

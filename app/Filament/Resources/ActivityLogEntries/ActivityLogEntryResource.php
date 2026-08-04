<?php

namespace App\Filament\Resources\ActivityLogEntries;

use App\Filament\Resources\ActivityLogEntries\Pages\ManageActivityLogEntries;
use App\Models\ActivityLogEntry;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityLogEntryResource extends Resource
{
    protected static ?string $model = ActivityLogEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = "Journal d'activité";

    protected static ?string $modelLabel = "entrée du journal d'activité";

    protected static ?string $pluralModelLabel = "Journal d'activité";

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('created_at')->label('Quand')->dateTime('d/m/Y H:i'),
            TextEntry::make('causer_name')->label('Par')->placeholder('Système'),
            TextEntry::make('subjectLabel')->label('Concerne')->state(fn (ActivityLogEntry $record) => static::subjectLabel($record)),
            TextEntry::make('event')->label('Action')->formatStateUsing(fn (string $state) => static::eventLabel($state)),
            KeyValueEntry::make('changesSummary')
                ->label('Modifications')
                ->state(fn (ActivityLogEntry $record) => collect($record->changes ?? [])
                    ->mapWithKeys(fn ($diff, $field) => [$field => ($diff['old'] ?? '—').' → '.($diff['new'] ?? '—')])
                    ->toArray())
                ->visible(fn (ActivityLogEntry $record) => filled($record->changes)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Quand')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('causer_name')
                    ->label('Par')
                    ->placeholder('Système')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::eventLabel($state))
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('subject_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('subject_label')
                    ->label('Concerne')
                    ->limit(50),
                TextColumn::make('changes')
                    ->label('Champs modifiés')
                    ->formatStateUsing(fn (?array $state): string => $state ? implode(', ', array_keys($state)) : '—')
                    ->limit(60),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Création',
                        'updated' => 'Modification',
                        'deleted' => 'Suppression',
                    ]),
                SelectFilter::make('subject_type')
                    ->label('Type')
                    ->options(fn () => ActivityLogEntry::query()
                        ->distinct()
                        ->pluck('subject_type', 'subject_type')
                        ->mapWithKeys(fn ($value) => [$value => class_basename($value)])),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivityLogEntries::route('/'),
        ];
    }

    private static function eventLabel(string $state): string
    {
        return match ($state) {
            'created' => 'Création',
            'updated' => 'Modification',
            'deleted' => 'Suppression',
            default => $state,
        };
    }

    private static function subjectLabel(ActivityLogEntry $record): string
    {
        return class_basename($record->subject_type).' — '.Str::limit((string) $record->subject_label, 60);
    }
}

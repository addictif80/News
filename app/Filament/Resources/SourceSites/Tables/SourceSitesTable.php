<?php

namespace App\Filament\Resources\SourceSites\Tables;

use App\Models\SourceSite;
use App\Services\VeilleService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class SourceSitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('base_url')
                    ->label('URL de base')
                    ->searchable(),
                TextColumn::make('rss_feed_url')
                    ->label('Flux RSS')
                    ->searchable()
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                IconColumn::make('used_for_watch')
                    ->label('Veille')
                    ->boolean(),
                TextColumn::make('last_polled_at')
                    ->label('Dernière vérification')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('pollNow')
                    ->label('Vérifier maintenant')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->visible(fn (SourceSite $record) => filled($record->rss_feed_url))
                    ->action(function (SourceSite $record, VeilleService $veille) {
                        $result = $veille->pollNow(collect([$record]));

                        Notification::make()
                            ->title('Vérification terminée')
                            ->body("{$result['imported']} article(s) importé(s).")
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('pollSelection')
                    ->label('Vérifier la sélection')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->action(function (Collection $records, VeilleService $veille) {
                        $result = $veille->pollNow($records);

                        Notification::make()
                            ->title('Vérification terminée')
                            ->body("{$result['polled']} site(s) vérifié(s), {$result['imported']} article(s) importé(s).")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

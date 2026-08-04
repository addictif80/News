<?php

namespace App\Filament\Resources\SourceSites\Tables;

use App\Models\Keyword;
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
                        if (self::warnIfNoActiveKeyword()) {
                            return;
                        }

                        $result = $veille->pollNow(collect([$record]));

                        self::notifyResult($result);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('pollSelection')
                    ->label('Vérifier la sélection')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->action(function (Collection $records, VeilleService $veille) {
                        if (self::warnIfNoActiveKeyword()) {
                            return;
                        }

                        self::notifyResult($veille->pollNow($records));
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return bool true if a warning was shown (caller should stop)
     */
    public static function warnIfNoActiveKeyword(): bool
    {
        if (Keyword::query()->where('is_active', true)->exists()) {
            return false;
        }

        Notification::make()
            ->title('Aucun mot-clé actif')
            ->body('La veille ne récupère un article que si son titre ou sa description contient au moins un mot-clé actif. Ajoute-en un dans « Veille informationnelle > Mots-clés ».')
            ->warning()
            ->send();

        return true;
    }

    /**
     * @param  array{polled: int, imported: int}  $result
     */
    public static function notifyResult(array $result): void
    {
        Notification::make()
            ->title('Vérification terminée')
            ->body("{$result['polled']} site(s) vérifié(s), {$result['imported']} article(s) importé(s). Détail dans « Journal de veille ».")
            ->success()
            ->send();
    }
}

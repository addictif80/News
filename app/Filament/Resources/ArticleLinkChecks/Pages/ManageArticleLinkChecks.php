<?php

namespace App\Filament\Resources\ArticleLinkChecks\Pages;

use App\Filament\Resources\ArticleLinkChecks\ArticleLinkCheckResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

class ManageArticleLinkChecks extends ManageRecords
{
    protected static string $resource = ArticleLinkCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runCheck')
                ->label('Lancer une vérification')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(function () {
                    Artisan::call('articles:check-links');

                    Notification::make()
                        ->title('Vérification terminée')
                        ->body(trim(Artisan::output()))
                        ->success()
                        ->send();
                }),
        ];
    }
}

<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('previewAsGuest')
                    ->label('Aperçu visiteur')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn () => route('articles.show', $this->record).'?preview_as=guest')
                    ->openUrlInNewTab(),
                Action::make('previewAsFree')
                    ->label('Aperçu membre gratuit')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn () => route('articles.show', $this->record).'?preview_as=free')
                    ->openUrlInNewTab(),
                Action::make('previewAsSubscriber')
                    ->label('Aperçu abonné')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn () => route('articles.show', $this->record).'?preview_as=subscriber')
                    ->openUrlInNewTab(),
            ])
                ->label('Prévisualiser')
                ->icon(Heroicon::OutlinedEye)
                ->button(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Exceptions\DuplicateImportException;
use App\Filament\Resources\Articles\ArticleResource;
use App\Services\ArticleImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importFromUrl')
                ->label('Importer depuis une URL')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->schema(fn (Schema $schema) => $schema->components([
                    TextInput::make('url')
                        ->label("URL de l'article")
                        ->url()
                        ->required(),
                ]))
                ->action(function (array $data, ArticleImportService $importer) {
                    try {
                        $article = $importer->importFromUrl($data['url']);

                        Notification::make()
                            ->title('Article importé : '.$article->title)
                            ->body('En attente de validation avant publication.')
                            ->success()
                            ->send();
                    } catch (DuplicateImportException $e) {
                        Notification::make()
                            ->title('Article déjà importé')
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title("Échec de l'import")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}

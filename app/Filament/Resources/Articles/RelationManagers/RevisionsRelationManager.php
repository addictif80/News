<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use App\Models\ArticleRevision;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $title = 'Historique des versions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Enregistré le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('editor.name')
                    ->label('Par')
                    ->placeholder('—'),
                TextColumn::make('title')
                    ->label('Titre à cette version')
                    ->limit(50),
            ])
            ->recordActions([
                Action::make('restore')
                    ->label('Restaurer cette version')
                    ->requiresConfirmation()
                    ->modalDescription("L'état actuel de l'article sera lui-même enregistré comme une nouvelle version avant la restauration, donc rien n'est perdu.")
                    ->action(function (ArticleRevision $record) {
                        $record->article->update([
                            'title' => $record->title,
                            'excerpt' => $record->excerpt,
                            'content' => $record->content,
                            'featured_image' => $record->featured_image,
                            'access_level' => $record->access_level,
                        ]);

                        Notification::make()
                            ->title('Version restaurée')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

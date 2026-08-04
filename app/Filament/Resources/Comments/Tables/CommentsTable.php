<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('article.title')
                    ->label('Article')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('user.name')
                    ->label('Auteur')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Message')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Posté le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approuver')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn (Comment $record) => $record->status !== 'approved')
                    ->action(fn (Comment $record) => $record->update(['status' => 'approved'])),
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->visible(fn (Comment $record) => $record->status !== 'rejected')
                    ->action(fn (Comment $record) => $record->update(['status' => 'rejected'])),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Approuver la sélection')
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved']))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('rejectSelected')
                        ->label('Rejeter la sélection')
                        ->icon(Heroicon::OutlinedXMark)
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected']))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

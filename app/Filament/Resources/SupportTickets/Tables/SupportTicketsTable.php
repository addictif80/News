<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'open' => 'Ouvert',
                        'pending' => 'En attente',
                        'closed' => 'Fermé',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'success',
                        'pending' => 'warning',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'low' => 'Basse',
                        'normal' => 'Normale',
                        'high' => 'Haute',
                        default => $state,
                    }),
                TextColumn::make('last_activity_at')
                    ->label('Dernière activité')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'open' => 'Ouvert',
                        'pending' => 'En attente',
                        'closed' => 'Fermé',
                    ]),
                SelectFilter::make('priority')
                    ->label('Priorité')
                    ->options([
                        'low' => 'Basse',
                        'normal' => 'Normale',
                        'high' => 'Haute',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_activity_at', 'desc');
    }
}

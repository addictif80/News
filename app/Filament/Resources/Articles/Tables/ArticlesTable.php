<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->label(''),
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'pending_review' => 'En attente',
                        'published' => 'Publié',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('access_level')
                    ->label('Accès')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => 'Membres',
                        'subscribers' => 'Abonnés',
                        default => 'Public',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'warning',
                        'subscribers' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                IconColumn::make('is_imported')
                    ->label('Importé')
                    ->boolean(),
                TextColumn::make('sourceSite.name')
                    ->label('Source')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('views_count')
                    ->label('Vues')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'pending_review' => 'En attente',
                        'published' => 'Publié',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_imported')
                    ->label('Importé'),
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
            ->defaultSort('created_at', 'desc');
    }
}

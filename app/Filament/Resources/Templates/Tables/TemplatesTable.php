<?php

namespace App\Filament\Resources\Templates\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'article' => 'Article',
                        'page' => 'Page',
                        'homepage' => "Page d'accueil",
                        default => $state,
                    }),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label('Par défaut')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('builder')
                    ->label('Design')
                    ->icon(Heroicon::OutlinedPaintBrush)
                    ->url(fn ($record) => route('filament.admin.resources.templates.builder', $record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

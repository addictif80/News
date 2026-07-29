<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('article_id')
                    ->label('Article')
                    ->relationship('article', 'title')
                    ->required(),
                Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('parent_id')
                    ->label('Réponse à')
                    ->relationship('parent', 'id'),
                Textarea::make('body')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}

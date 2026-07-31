<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('support_category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'open' => 'Ouvert',
                        'pending' => 'En attente',
                        'closed' => 'Fermé',
                    ])
                    ->required(),
                Select::make('priority')
                    ->label('Priorité')
                    ->options([
                        'low' => 'Basse',
                        'normal' => 'Normale',
                        'high' => 'Haute',
                    ])
                    ->required(),
            ]);
    }
}

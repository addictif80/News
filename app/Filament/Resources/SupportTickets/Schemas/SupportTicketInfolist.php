<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupportTicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('user.name')->label('Utilisateur'),
                        TextEntry::make('category.name')->label('Catégorie')->placeholder('—'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'open' => 'Ouvert',
                                'pending' => 'En attente',
                                'closed' => 'Fermé',
                                default => $state,
                            }),
                        TextEntry::make('priority')
                            ->label('Priorité')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'low' => 'Basse',
                                'normal' => 'Normale',
                                'high' => 'Haute',
                                default => $state,
                            }),
                    ]),
                RepeatableEntry::make('messages')
                    ->label('Conversation')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('')
                            ->formatStateUsing(fn ($record) => $record->is_staff_reply ? 'Support' : $record->user->name)
                            ->weight('bold'),
                        TextEntry::make('created_at')->label('')->dateTime('d/m/Y H:i'),
                        TextEntry::make('body')->label('')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

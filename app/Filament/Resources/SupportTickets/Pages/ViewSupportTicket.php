<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Répondre')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema(fn (Schema $schema) => $schema->components([
                    Textarea::make('body')
                        ->label('Message')
                        ->rows(4)
                        ->required(),
                ]))
                ->action(function (array $data) {
                    $this->record->messages()->create([
                        'user_id' => auth()->id(),
                        'body' => $data['body'],
                        'is_staff_reply' => true,
                    ]);

                    $this->record->update([
                        'status' => 'pending',
                        'last_activity_at' => now(),
                    ]);

                    Notification::make()->title('Réponse envoyée')->success()->send();
                }),
            EditAction::make(),
        ];
    }
}

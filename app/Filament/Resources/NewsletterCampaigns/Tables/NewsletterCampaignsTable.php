<?php

namespace App\Filament\Resources\NewsletterCampaigns\Tables;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterSubscriber;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsletterCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Objet')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'Brouillon',
                        'sending' => 'Envoi en cours',
                        'sent' => 'Envoyé',
                        default => $state,
                    }),
                TextColumn::make('scheduled_at')
                    ->label('Programmé pour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->label('Envoyé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('recipients_count')
                    ->label('Destinataires')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Envoyer maintenant')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(function ($record) {
                        $record->update(['status' => 'sending']);
                        SendNewsletterCampaignJob::dispatch($record);

                        Notification::make()
                            ->title('Envoi lancé pour '.NewsletterSubscriber::where('is_confirmed', true)->whereNull('unsubscribed_at')->count().' abonnés')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

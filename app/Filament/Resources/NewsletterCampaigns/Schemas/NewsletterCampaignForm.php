<?php

namespace App\Filament\Resources\NewsletterCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NewsletterCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Objet')
                    ->required(),
                RichEditor::make('body_html')
                    ->label('Contenu')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sending' => 'Envoi en cours',
                        'sent' => 'Envoyé',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('scheduled_at')->label('Programmé pour'),
            ]);
    }
}

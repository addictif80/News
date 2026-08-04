<?php

namespace App\Filament\Pages;

use App\Settings\CommentModerationSettings;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageCommentModerationSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static UnitEnum|string|null $navigationGroup = 'Réglages';

    protected static ?string $title = 'Modération des commentaires';

    protected static string $settings = CommentModerationSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'moderateur']) ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TagsInput::make('blocked_words')
                    ->label('Mots interdits')
                    ->helperText('Un commentaire contenant un de ces mots est automatiquement rejeté au lieu de partir en attente de modération.')
                    ->placeholder('Ajouter un mot et appuyer sur Entrée'),
            ]);
    }
}

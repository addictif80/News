<?php

namespace App\Filament\Pages;

use App\Settings\WebPushSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Minishlink\WebPush\VAPID;
use UnitEnum;

class ManageWebPushSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static UnitEnum|string|null $navigationGroup = 'Réglages';

    protected static ?string $title = 'Notifications push';

    protected static string $settings = WebPushSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateVapidKeys')
                ->label('Générer des clés VAPID')
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Cela remplacera les clés existantes. Les abonnements push déjà enregistrés par les visiteurs cesseront de fonctionner et devront être réactivés.')
                ->action(function () {
                    $keys = VAPID::createVapidKeys();

                    $this->form->fill(array_merge($this->form->getState(), [
                        'public_key' => $keys['publicKey'],
                        'private_key' => $keys['privateKey'],
                    ]));

                    Notification::make()
                        ->title('Nouvelles clés VAPID générées — pense à enregistrer.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_enabled')
                    ->label('Activer les notifications push'),
                TextInput::make('subject')
                    ->label('Identifiant (URL ou mailto:)')
                    ->helperText('URL du site ou adresse email au format mailto:contact@exemple.fr')
                    ->required(),
                TextInput::make('public_key')
                    ->label('Clé publique VAPID')
                    ->helperText('Utilisée côté navigateur — génère-la avec le bouton ci-dessus si tu ne l\'as pas.')
                    ->required(),
                TextInput::make('private_key')
                    ->label('Clé privée VAPID')
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }
}

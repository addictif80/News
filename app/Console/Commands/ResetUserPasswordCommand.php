<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('user:reset-password {email} {--password=}')]
#[Description("Reset a user's password from the command line — useful when SMTP isn't configured yet and the web reset flow is unavailable.")]
class ResetUserPasswordCommand extends Command
{
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("Aucun utilisateur avec l'email « {$this->argument('email')} ».");

            return self::FAILURE;
        }

        $password = $this->option('password') ?: Str::password(16);

        $user->forceFill(['password' => Hash::make($password)])->save();

        $this->info("Mot de passe mis à jour pour {$user->email}.");

        if (! $this->option('password')) {
            $this->warn("Nouveau mot de passe généré : {$password}");
            $this->warn('Connectez-vous puis changez-le immédiatement depuis /compte.');
        }

        return self::SUCCESS;
    }
}

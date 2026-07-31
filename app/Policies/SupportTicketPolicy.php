<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SupportTicket $supportTicket): bool
    {
        return $user->id === $supportTicket->user_id || $user->hasRole('moderateur');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function reply(User $user, SupportTicket $supportTicket): bool
    {
        return $this->view($user, $supportTicket);
    }

    public function update(User $user, SupportTicket $supportTicket): bool
    {
        return $user->hasRole('moderateur');
    }

    public function delete(User $user, SupportTicket $supportTicket): bool
    {
        return $user->hasRole('moderateur');
    }
}

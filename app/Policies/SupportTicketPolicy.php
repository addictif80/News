<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    /**
     * Used by the admin panel's resource navigation/list — staff only.
     * Public-site access (a member listing their own tickets) doesn't go
     * through this policy; see TicketController.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:SupportTicket');
    }

    /**
     * True for the ticket's own author (public site) or staff with the
     * Shield permission (admin panel).
     */
    public function view(User $user, SupportTicket $supportTicket): bool
    {
        return $user->id === $supportTicket->user_id || $user->can('View:SupportTicket');
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
        return $user->can('Update:SupportTicket');
    }

    public function delete(User $user, SupportTicket $supportTicket): bool
    {
        return $user->can('Delete:SupportTicket');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:SupportTicket');
    }
}

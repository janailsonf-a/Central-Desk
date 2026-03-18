<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->company_id !== $ticket->company_id) {
            return false;
        }

        if ($user->isAdmin() || $user->isGestor()) {
            return true;
        }

        if ($user->isTecnico()) {
            return $ticket->assigned_to === $user->id || $ticket->company_id === $user->company_id;
        }

        return $ticket->requester_id === $user->id;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->company_id !== $ticket->company_id) {
            return false;
        }

        if ($user->isAdmin() || $user->isGestor()) {
            return true;
        }

        if ($user->isTecnico()) {
            return $ticket->assigned_to === $user->id;
        }

        return $ticket->requester_id === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->company_id !== $ticket->company_id) {
            return false;
        }

        return $user->isAdmin() || $user->isGestor();
    }
}
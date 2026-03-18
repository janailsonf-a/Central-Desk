<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;

class TicketHistoryService
{
    public function create(
        Ticket $ticket,
        ?User $user,
        string $action,
        ?string $description = null,
        ?array $oldValue = null,
        ?array $newValue = null,
    ): TicketHistory {
        return TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
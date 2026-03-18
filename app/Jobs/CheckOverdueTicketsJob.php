<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckOverdueTicketsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Ticket::query()
            ->whereNotNull('due_at')
            ->where('is_overdue', false)
            ->where('due_at', '<', now())
            ->update(['is_overdue' => true]);
    }
}
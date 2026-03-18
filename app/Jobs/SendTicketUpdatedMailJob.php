<?php

namespace App\Jobs;

use App\Mail\TicketStatusUpdatedMail;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTicketUpdatedMailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $ticketId)
    {
    }

    public function handle(): void
    {
        $ticket = Ticket::with(['requester', 'status'])->find($this->ticketId);

        if (! $ticket || ! $ticket->requester?->email) {
            return;
        }

        Mail::to($ticket->requester->email)->send(new TicketStatusUpdatedMail($ticket));
    }
}
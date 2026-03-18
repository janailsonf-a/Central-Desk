<?php

namespace App\Services;

use App\Jobs\SendTicketCreatedMailJob;
use App\Jobs\SendTicketUpdatedMailJob;
use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function __construct(
        private TicketHistoryService $historyService,
        private SlaService $slaService
    ) {
    }

    public function create(array $data, User $user): Ticket
    {
        return DB::transaction(function () use ($data, $user) {
            $category = Category::where('company_id', $user->company_id)
                ->findOrFail($data['category_id']);

            $priority = isset($data['priority_id'])
                ? Priority::findOrFail($data['priority_id'])
                : Priority::where('slug', 'media')->firstOrFail();

            $department = null;
            if (! empty($data['department_id'])) {
                $department = Department::where('company_id', $user->company_id)
                    ->findOrFail($data['department_id']);
            }

            $openStatus = TicketStatus::where('slug', 'aberto')->firstOrFail();
            $dueAt = $this->slaService->calculateDueAt($user->company_id, $priority);

            $ticket = Ticket::create([
                'company_id' => $user->company_id,
                'protocol' => $this->generateProtocol(),
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $category->id,
                'priority_id' => $priority->id,
                'status_id' => $openStatus->id,
                'requester_id' => $user->id,
                'assigned_to' => null,
                'department_id' => $department?->id,
                'opened_at' => now(),
                'due_at' => $dueAt,
                'resolved_at' => null,
                'closed_at' => null,
                'is_overdue' => $this->slaService->isOverdue($dueAt),
            ]);

            $this->historyService->create(
                ticket: $ticket,
                user: $user,
                action: 'ticket_created',
                description: 'Chamado criado com sucesso.',
                oldValue: null,
                newValue: [
                    'protocol' => $ticket->protocol,
                    'title' => $ticket->title,
                    'status_id' => $ticket->status_id,
                    'due_at' => $ticket->due_at?->toDateTimeString(),
                ]
            );

            dispatch(new SendTicketCreatedMailJob($ticket->id));

            return $ticket->load([
                'category',
                'priority',
                'status',
                'requester',
                'technician',
                'department',
            ]);
        });
    }

    public function assign(Ticket $ticket, User $actor, User $technician): Ticket
    {
        if (! $actor->isAdmin() && ! $actor->isGestor()) {
            throw ValidationException::withMessages([
                'assigned_to' => 'Sem permissão para atribuir técnico.',
            ]);
        }

        if ($technician->company_id !== $actor->company_id || ! $technician->isTecnico()) {
            throw ValidationException::withMessages([
                'assigned_to' => 'Técnico inválido para esta empresa.',
            ]);
        }

        $oldAssigned = $ticket->assigned_to;

        $ticket->update([
            'assigned_to' => $technician->id,
        ]);

        $this->historyService->create(
            ticket: $ticket,
            user: $actor,
            action: 'ticket_assigned',
            description: "Chamado atribuído para {$technician->name}.",
            oldValue: ['assigned_to' => $oldAssigned],
            newValue: ['assigned_to' => $technician->id]
        );

        return $ticket->load(['technician']);
    }

    public function changeStatus(Ticket $ticket, User $actor, TicketStatus $status): Ticket
    {
        $oldStatus = $ticket->status_id;

        $ticket->status_id = $status->id;

        if ($status->slug === 'resolvido') {
            $ticket->resolved_at = now();
        }

        if ($status->slug === 'fechado') {
            $ticket->closed_at = now();
        }

        $ticket->save();

        $this->historyService->create(
            ticket: $ticket,
            user: $actor,
            action: 'ticket_status_changed',
            description: "Status alterado para {$status->name}.",
            oldValue: ['status_id' => $oldStatus],
            newValue: ['status_id' => $status->id]
        );

        dispatch(new SendTicketUpdatedMailJob($ticket->id));

        return $ticket->load(['status']);
    }

    public function addComment(Ticket $ticket, User $actor, array $data): TicketComment
    {
        if (($data['is_internal'] ?? false) && $actor->isSolicitante()) {
            throw ValidationException::withMessages([
                'is_internal' => 'Solicitante não pode criar comentário interno.',
            ]);
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $actor->id,
            'comment' => $data['comment'],
            'is_internal' => $data['is_internal'] ?? false,
        ]);

        $this->historyService->create(
            ticket: $ticket,
            user: $actor,
            action: 'ticket_comment_added',
            description: 'Comentário adicionado ao chamado.',
            oldValue: null,
            newValue: ['comment_id' => $comment->id]
        );

        return $comment->load('user');
    }

    private function generateProtocol(): string
    {
        $date = now()->format('Ymd');
        $count = Ticket::whereDate('created_at', now()->toDateString())->count() + 1;

        return sprintf('TCK-%s-%04d', $date, $count);
    }
}
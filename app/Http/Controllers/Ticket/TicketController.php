<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\AssignTicketRequest;
use App\Http\Requests\Ticket\ChangeTicketStatusRequest;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $authUser = $request->user();

        $query = Ticket::with([
            'category',
            'priority',
            'status',
            'requester',
            'technician',
            'department',
        ])
            ->where('company_id', $authUser->company_id)
            ->when($authUser->isSolicitante(), fn ($q) => $q->where('requester_id', $authUser->id))
            ->when($request->filled('status_id'), fn ($q) => $q->where('status_id', $request->integer('status_id')))
            ->when($request->filled('priority_id'), fn ($q) => $q->where('priority_id', $request->integer('priority_id')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('protocol'), fn ($q) => $q->where('protocol', 'like', '%' . $request->input('protocol') . '%'))
            ->latest();

        return response()->json($query->paginate());
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create($request->validated(), $request->user());

        return response()->json($ticket, 201);
    }

    public function show(Request $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('view', $item);

        if ($request->user()->isSolicitante()) {
            $item->setRelation(
                'comments',
                $item->comments->where('is_internal', false)->values()
            );
        }

        return response()->json($item);
    }

    public function update(UpdateTicketRequest $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $item->update($request->validated());

        return response()->json(
            $item->fresh()->load(['category', 'priority', 'status', 'department'])
        );
    }

    public function assign(AssignTicketRequest $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $technician = $this->findTechnicianOrFail(
            $request->user()->company_id,
            $request->integer('assigned_to')
        );

        $item = $this->ticketService->assign($item, $request->user(), $technician);

        return response()->json($item);
    }

    public function changeStatus(ChangeTicketStatusRequest $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $status = TicketStatus::findOrFail($request->integer('status_id'));

        $item = $this->ticketService->changeStatus($item, $request->user(), $status);

        return response()->json($item);
    }

    public function destroy(Request $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('delete', $item);

        $item->delete();

        return response()->json([
            'message' => 'Chamado excluído com sucesso.',
        ]);
    }

    private function findTicketOrFail(int $companyId, int $ticketId): Ticket
    {
        return Ticket::with([
            'category',
            'priority',
            'status',
            'requester',
            'technician',
            'department',
            'comments.user',
            'attachments.user',
            'histories.user',
            ])
            ->where('company_id', $companyId)
            ->findOrFail($ticketId);
    }

    private function findTechnicianOrFail(int $companyId, int $userId): User
    {
        return User::where('company_id', $companyId)
            ->where('id', $userId)
            ->firstOrFail();
    }
}
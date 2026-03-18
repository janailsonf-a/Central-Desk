<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketCommentRequest;
use App\Http\Requests\Ticket\UpdateTicketCommentRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\TicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
    }

    public function index(Request $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('view', $item);

        $comments = $item->comments()
            ->when(
                $request->user()->isSolicitante(),
                fn ($q) => $q->where('is_internal', false)
            )
            ->with('user')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(StoreTicketCommentRequest $request, int $ticket): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $comment = $this->ticketService->addComment(
            $item,
            $request->user(),
            $request->validated()
        );

        return response()->json($comment, 201);
    }

    public function show(Request $request, int $ticket, int $comment): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('view', $item);

        $ticketComment = TicketComment::where('ticket_id', $item->id)
            ->where('id', $comment)
            ->when(
                $request->user()->isSolicitante(),
                fn ($q) => $q->where('is_internal', false)
            )
            ->with('user')
            ->firstOrFail();

        return response()->json($ticketComment);
    }

    public function update(UpdateTicketCommentRequest $request, int $ticket, int $comment): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $ticketComment = TicketComment::where('ticket_id', $item->id)
            ->where('id', $comment)
            ->firstOrFail();

        $this->authorizeCommentOwner($ticketComment, $request->user());

        $ticketComment->update($request->validated());

        return response()->json($ticketComment->fresh()->load('user'));
    }

    public function destroy(Request $request, int $ticket, int $comment): JsonResponse
    {
        $item = $this->findTicketOrFail($request->user()->company_id, $ticket);

        $this->authorize('update', $item);

        $ticketComment = TicketComment::where('ticket_id', $item->id)
            ->where('id', $comment)
            ->firstOrFail();

        $this->authorizeCommentOwner($ticketComment, $request->user());

        $ticketComment->delete();

        return response()->json([
            'message' => 'Comentário excluído com sucesso.',
        ]);
    }

    private function findTicketOrFail(int $companyId, int $ticketId): Ticket
    {
        return Ticket::where('company_id', $companyId)->findOrFail($ticketId);
    }

    /**
     * @throws AuthorizationException
     */
    private function authorizeCommentOwner(TicketComment $comment, $user): void
    {
        if ($comment->user_id !== $user->id && ! $user->isAdmin()) {
            throw new AuthorizationException('Você não tem permissão para alterar este comentário.');
        }
    }
}
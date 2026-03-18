<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketAttachmentRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\TicketHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function __construct(private TicketHistoryService $historyService)
    {
    }

    public function index(Request $request, int $ticket): JsonResponse
    {
        $item = Ticket::where('company_id', $request->user()->company_id)->findOrFail($ticket);

        $this->authorize('view', $item);

        $attachments = $item->attachments()->with('user')->latest()->get();

        return response()->json($attachments);
    }

    public function store(StoreTicketAttachmentRequest $request, int $ticket): JsonResponse
    {
        $item = Ticket::where('company_id', $request->user()->company_id)->findOrFail($ticket);

        $this->authorize('update', $item);

        $file = $request->file('file');
        $path = $file->store('ticket-attachments', 'public');

        $attachment = TicketAttachment::create([
            'ticket_id' => $item->id,
            'user_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $this->historyService->create(
            ticket: $item,
            user: $request->user(),
            action: 'ticket_attachment_added',
            description: 'Anexo enviado para o chamado.',
            oldValue: null,
            newValue: [
                'attachment_id' => $attachment->id,
                'original_name' => $attachment->original_name,
            ]
        );

        return response()->json($attachment->load('user'), 201);
    }

    public function destroy(Request $request, int $ticket, int $attachment): JsonResponse
    {
        $item = Ticket::where('company_id', $request->user()->company_id)->findOrFail($ticket);

        $this->authorize('update', $item);

        $file = TicketAttachment::where('ticket_id', $item->id)->findOrFail($attachment);

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        $this->historyService->create(
            ticket: $item,
            user: $request->user(),
            action: 'ticket_attachment_deleted',
            description: 'Anexo removido do chamado.',
            oldValue: ['attachment_id' => $attachment],
            newValue: null
        );

        return response()->json([
            'message' => 'Anexo removido com sucesso.',
        ]);
    }
}
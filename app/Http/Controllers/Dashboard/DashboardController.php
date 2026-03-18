<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna métricas gerais de tickets.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function metrics(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $metrics = [
            'total' => $this->getTicketCountByStatus($companyId, null),
            'open' => $this->getTicketCountByStatus($companyId, 'aberto'),
            'in_progress' => $this->getTicketCountByStatus($companyId, 'em_andamento'),
            'resolved' => $this->getTicketCountByStatus($companyId, 'resolvido'),
            'closed' => $this->getTicketCountByStatus($companyId, 'fechado'),
        ];

        return response()->json($metrics);
    }

    /**
     * Retorna contagem de tickets agrupados por status.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byStatus(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $items = Ticket::query()
            ->select('ticket_statuses.name', 'ticket_statuses.slug', DB::raw('count(tickets.id) as total'))
            ->join('ticket_statuses', 'ticket_statuses.id', '=', 'tickets.status_id')
            ->where('tickets.company_id', $companyId)
            ->groupBy('ticket_statuses.id', 'ticket_statuses.name', 'ticket_statuses.slug')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($items);
    }

    /**
     * Retorna métricas por prioridade.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byPriority(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $items = Ticket::query()
            ->select('priorities.name', 'priorities.slug', DB::raw('count(tickets.id) as total'))
            ->join('priorities', 'priorities.id', '=', 'tickets.priority_id')
            ->where('tickets.company_id', $companyId)
            ->groupBy('priorities.id', 'priorities.name', 'priorities.slug')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($items);
    }

    /**
     * Retorna métricas por categoria.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byCategory(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $items = Ticket::query()
            ->select('categories.name', DB::raw('count(tickets.id) as total'))
            ->join('categories', 'categories.id', '=', 'tickets.category_id')
            ->where('tickets.company_id', $companyId)
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($items);
    }

    /**
     * Retorna métricas por técnico atribuído.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byTechnician(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $items = Ticket::query()
            ->select(DB::raw("COALESCE(CONCAT(users.name), 'Não Atribuído') as technician_name"), 
                DB::raw('count(tickets.id) as total'))
            ->leftJoin('users', 'users.id', '=', 'tickets.assigned_to')
            ->where('tickets.company_id', $companyId)
            ->groupBy('tickets.assigned_to', 'users.name')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($items);
    }

    /**
     * Retorna tickets recentes.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recentTickets(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $limit = $request->integer('limit', 10);

        $tickets = Ticket::where('company_id', $companyId)
            ->with(['status', 'priority', 'requester', 'technician'])
            ->latest('created_at')
            ->limit($limit)
            ->get();

        return response()->json($tickets);
    }

    /**
     * Retorna estatísticas de tempo médio de resolução.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function avgResolutionTime(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $avgTime = Ticket::where('company_id', $companyId)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(EXTRACT(DAY FROM (resolved_at - created_at))) as avg_days')
            ->first();

        return response()->json([
            'avg_days' => $avgTime?->avg_days ?? 0,
        ]);
    }

    /**
     * Retorna dashboard completo com todas as métricas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function overview(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $overview = [
            'metrics' => [
                'total' => $this->getTicketCountByStatus($companyId, null),
                'open' => $this->getTicketCountByStatus($companyId, 'aberto'),
                'in_progress' => $this->getTicketCountByStatus($companyId, 'em_andamento'),
                'resolved' => $this->getTicketCountByStatus($companyId, 'resolvido'),
                'closed' => $this->getTicketCountByStatus($companyId, 'fechado'),
            ],
            'by_status' => $this->getByStatus($companyId),
            'by_priority' => $this->getByPriority($companyId),
            'recent_tickets' => $this->getRecentTickets($companyId, 5),
        ];

        return response()->json($overview);
    }

    /**
     * Obtém a contagem de tickets por status específico.
     *
     * @param int $companyId
     * @param string|null $slug Status slug ou null para total
     * @return int
     */
    private function getTicketCountByStatus(int $companyId, ?string $slug): int
    {
        $query = Ticket::where('company_id', $companyId);

        if ($slug) {
            $query->whereHas('status', fn ($q) => $q->where('slug', $slug));
        }

        return $query->count();
    }

    /**
     * Obtém tickets agrupados por status.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getByStatus(int $companyId)
    {
        return Ticket::query()
            ->select('ticket_statuses.name', 'ticket_statuses.slug', DB::raw('count(tickets.id) as total'))
            ->join('ticket_statuses', 'ticket_statuses.id', '=', 'tickets.status_id')
            ->where('tickets.company_id', $companyId)
            ->groupBy('ticket_statuses.id', 'ticket_statuses.name', 'ticket_statuses.slug')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Obtém tickets agrupados por prioridade.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getByPriority(int $companyId)
    {
        return Ticket::query()
            ->select('priorities.name', 'priorities.slug', DB::raw('count(tickets.id) as total'))
            ->join('priorities', 'priorities.id', '=', 'tickets.priority_id')
            ->where('tickets.company_id', $companyId)
            ->groupBy('priorities.id', 'priorities.name', 'priorities.slug')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Obtém tickets recentes.
     *
     * @param int $companyId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentTickets(int $companyId, int $limit)
    {
        return Ticket::where('company_id', $companyId)
            ->with(['status', 'priority', 'requester', 'technician'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
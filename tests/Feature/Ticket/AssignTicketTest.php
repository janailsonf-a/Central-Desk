<?php

namespace Tests\Feature\Ticket;

use App\Models\Sla;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Helpers\CreatesCentralDeskData;
use Tests\TestCase;

class AssignTicketTest extends TestCase
{
    use RefreshDatabase;
    use CreatesCentralDeskData;

    public function test_admin_can_assign_ticket_to_technician(): void
    {
        $data = $this->createBaseData();

        Sla::create([
            'company_id' => $data['company']->id,
            'priority_id' => $data['priorityMedia']->id,
            'first_response_minutes' => 120,
            'resolution_minutes' => 1440,
            'active' => true,
        ]);

        $ticket = Ticket::create([
            'company_id' => $data['company']->id,
            'protocol' => 'TCK-20260318-0001',
            'title' => 'Teste',
            'description' => 'Teste',
            'category_id' => $data['category']->id,
            'priority_id' => $data['priorityMedia']->id,
            'status_id' => $data['statusAberto']->id,
            'requester_id' => $data['admin']->id,
            'department_id' => $data['department']->id,
            'opened_at' => now(),
            'is_overdue' => false,
        ]);

        $response = $this->actingAs($data['admin'], 'sanctum')->postJson("/api/tickets/{$ticket->id}/assign", [
            'assigned_to' => $data['tecnico']->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('technician.id', $data['tecnico']->id);
    }
}
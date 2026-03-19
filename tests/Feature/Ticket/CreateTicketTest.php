<?php

namespace Tests\Feature\Ticket;

use App\Models\Sla;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Helpers\CreatesCentralDeskData;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;
    use CreatesCentralDeskData;

    public function test_authenticated_user_can_create_ticket(): void
    {
        $data = $this->createBaseData();

        Sla::create([
            'company_id' => $data['company']->id,
            'priority_id' => $data['priorityMedia']->id,
            'first_response_minutes' => 120,
            'resolution_minutes' => 1440,
            'active' => true,
        ]);

        $response = $this->actingAs($data['admin'], 'sanctum')->postJson('/api/tickets', [
            'title' => 'Erro ao acessar sistema',
            'description' => 'Erro 500 ao abrir dashboard',
            'category_id' => $data['category']->id,
            'priority_id' => $data['priorityMedia']->id,
            'department_id' => $data['department']->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('title', 'Erro ao acessar sistema')
            ->assertJsonPath('status.slug', 'aberto');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Erro ao acessar sistema',
            'company_id' => $data['company']->id,
        ]);
    }
}
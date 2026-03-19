<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Helpers\CreatesCentralDeskData;
use Tests\TestCase;

class CommentTicketTest extends TestCase
{
    use RefreshDatabase;
    use CreatesCentralDeskData;

    public function test_user_can_comment_on_ticket(): void
    {
        $data = $this->createBaseData();

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

        $response = $this->actingAs($data['admin'], 'sanctum')->postJson("/api/tickets/{$ticket->id}/comments", [
            'comment' => 'Comentário de teste',
            'is_internal' => false,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('comment', 'Comentário de teste');

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'comment' => 'Comentário de teste',
        ]);
    }
}
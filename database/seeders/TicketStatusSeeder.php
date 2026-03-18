<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Aberto', 'slug' => 'aberto', 'color' => '#2563eb'],
            ['name' => 'Aguardando Atendimento', 'slug' =>
            'aguardando_atendimento', 'color' => '#f59e0b'],
            ['name' => 'Em Andamento', 'slug' => 'em_andamento', 'color' =>
            '#0ea5e9'],
            [
                'name' => 'Aguardando Usuário',
                'slug' => 'aguardando_usuario',
                'color' => '#8b5cf6'
            ],
            ['name' => 'Resolvido', 'slug' => 'resolvido', 'color' =>
            '#10b981'],
            ['name' => 'Fechado', 'slug' => 'fechado', 'color' => '#16a34a'],
            ['name' => 'Cancelado', 'slug' => 'cancelado', 'color' =>
            '#ef4444'],
        ];
        foreach ($items as $item) {
            TicketStatus::firstOrCreate(['slug' => $item['slug']], $item);
        }
    }
}

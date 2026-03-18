<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Baixa', 'slug' => 'baixa', 'level' => 1],
            ['name' => 'Média', 'slug' => 'media', 'level' => 2],
            ['name' => 'Alta', 'slug' => 'alta', 'level' => 3],
            ['name' => 'Crítica', 'slug' => 'critica', 'level' => 4],
        ];
        foreach ($items as $item) {
            Priority::firstOrCreate(['slug' => $item['slug']], $item);
        }
    }
}

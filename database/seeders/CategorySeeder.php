<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $items = [
            ['name' => 'Infraestrutura', 'description' =>
            'Problemas de rede, máquina e hardware'],
            ['name' => 'Sistema', 'description' => 'Erros e bugs no
sistema'],
        ];
        foreach ($items as $item) {
            Category::firstOrCreate(
                ['company_id' => $company->id, 'name' => $item['name']],
                [...$item, 'active' => true]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $items = [
            ['name' => 'TI', 'description' => 'Tecnologia da informação'],
            ['name' => 'RH', 'description' => 'Recursos humanos'],
        ];
        foreach ($items as $item) {
            Department::firstOrCreate(
                ['company_id' => $company->id, 'name' => $item['name']],
                [...$item, 'active' => true]
            );
        }
    }
}

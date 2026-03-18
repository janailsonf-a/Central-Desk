<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            RoleSeeder::class,
            PrioritySeeder::class,
            TicketStatusSeeder::class,
            DepartmentSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            SlaSeeder::class,
        ]);
    }
}

<?php

namespace Tests\Feature\Helpers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Role;
use App\Models\TicketStatus;
use App\Models\User;

trait CreatesCentralDeskData
{
    protected function createBaseData(): array
    {
        $company = Company::factory()->create();

        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $gestorRole = Role::create(['name' => 'Gestor', 'slug' => 'gestor']);
        $tecnicoRole = Role::create(['name' => 'Tecnico', 'slug' => 'tecnico']);
        $solicitanteRole = Role::create(['name' => 'Solicitante', 'slug' => 'solicitante']);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
            'email' => 'admin@test.com',
            'password' => '123456',
        ]);

        $tecnico = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $tecnicoRole->id,
        ]);

        $solicitante = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $solicitanteRole->id,
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => 'Infraestrutura',
            'description' => 'Categoria teste',
            'active' => true,
        ]);

        $department = Department::create([
            'company_id' => $company->id,
            'name' => 'TI',
            'description' => 'Departamento teste',
            'active' => true,
        ]);

        $priorityBaixa = Priority::create(['name' => 'Baixa', 'slug' => 'baixa', 'level' => 1]);
        $priorityMedia = Priority::create(['name' => 'Média', 'slug' => 'media', 'level' => 2]);

        $statusAberto = TicketStatus::create([
            'name' => 'Aberto',
            'slug' => 'aberto',
            'color' => '#2563eb',
        ]);

        $statusAndamento = TicketStatus::create([
            'name' => 'Em Andamento',
            'slug' => 'em_andamento',
            'color' => '#0ea5e9',
        ]);

        return compact(
            'company',
            'admin',
            'tecnico',
            'solicitante',
            'category',
            'department',
            'priorityBaixa',
            'priorityMedia',
            'statusAberto',
            'statusAndamento',
            'adminRole',
            'gestorRole',
            'tecnicoRole',
            'solicitanteRole',
        );
    }
}
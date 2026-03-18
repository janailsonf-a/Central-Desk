<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $users = [
            [
                'name' => 'Admin Demo',
                'email' => 'admin@centraldesk.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Gestor Demo',
                'email' => 'gestor@centraldesk.com',
                'role' => 'gestor',
            ],
            [
                'name' => 'Técnico Demo',
                'email' => 'tecnico@centraldesk.com',
                'role' => 'tecnico',
            ],
            [
                'name' => 'Solicitante Demo',
                'email' => 'solicitante@centraldesk.com',
                'role' => 'solicitante',
            ],
        ];
        foreach ($users as $item) {
            $role = Role::where('slug', $item['role'])->firstOrFail();
            User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('123456'),
                    'company_id' => $company->id,
                    'role_id' => $role->id,
                    'phone' => null,
                    'active' => true,
                ]
            );
        }
    }
}

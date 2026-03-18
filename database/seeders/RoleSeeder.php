<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRoleEnum::cases() as $role) {
            Role::firstOrCreate(
                ['slug' => $role->value],
                ['name' => ucfirst($role->value)]
            );
        }
    }
}

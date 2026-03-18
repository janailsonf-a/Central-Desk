<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $company = Company::factory()->create();
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id,
            'email' => 'admin@test.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => '123456',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user']);
    }
}
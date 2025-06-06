<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_fazer_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_usuario_nao_pode_fazer_login_com_credenciais_invalidas()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'naoexiste@teste.com',
            'password' => 'senhainvalida',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_usuario_pode_fazer_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout realizado com sucesso']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_rota_protegida_requer_autenticacao()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}

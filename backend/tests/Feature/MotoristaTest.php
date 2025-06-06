<?php

namespace Tests\Feature;

use App\Models\Motorista;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotoristaTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    private function getAuthHeaders()
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    public function test_listar_motoristas()
    {
        // Criar 3 motoristas
        $motoristas = Motorista::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/motoristas');

        $response->assertStatus(200)
            ->assertJsonCount(3); // Removido o 'data' do assertJsonCount
    }

    public function test_criar_motorista()
    {
        $dados = [
            'nome' => 'João Silva',
            'cpf' => '123.456.789-00',
            'data_nascimento' => '1990-01-01',
            'categoria_cnh' => 'AB',
            'telefone' => '(11) 99999-9999',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/motoristas', $dados);

        // Adicione esta linha para ver o erro de validação
        if ($response->status() === 422) {
            dd($response->json());
        }

        $response->assertStatus(201)
            ->assertJson($dados);
    }

    public function test_validacao_ao_criar_motorista()
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/motoristas', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'cpf', 'data_nascimento', 'categoria_cnh', 'telefone']);
    }

    public function test_mostrar_motorista()
    {
        $motorista = Motorista::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson("/api/motoristas/{$motorista->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $motorista->id]);
    }

    public function test_atualizar_motorista()
    {
        $motorista = Motorista::factory()->create();

        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
            'telefone' => '(11) 88888-8888',
            'cpf' => '123.456.789-00', // Adicionei o CPF para garantir que está sendo validado
            'data_nascimento' => '1990-01-01', // Adicionei a data de nascimento
            'categoria_cnh' => 'B', // Adicionei a categoria da CNH
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->putJson("/api/motoristas/{$motorista->id}", $dadosAtualizados);

        // Adicione esta linha para ver o erro de validação
        if ($response->status() !== 200) {
            dd($response->json());
        }

        $response->assertStatus(200)
            ->assertJsonFragment($dadosAtualizados);
    }

    public function test_deletar_motorista()
    {
        $motorista = Motorista::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->deleteJson("/api/motoristas/{$motorista->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('motoristas', ['id' => $motorista->id]);
    }

    public function test_apenas_usuarios_autenticados_podem_gerenciar_motoristas()
    {
        $response = $this->getJson('/api/motoristas');
        $response->assertStatus(401);
    }
}

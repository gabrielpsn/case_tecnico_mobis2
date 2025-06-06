<?php

namespace Tests\Feature;

use App\Models\Motorista;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VeiculoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function getAuthHeaders(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        return ['Authorization' => "Bearer $token"];
    }

    public function test_listar_veiculos()
    {
        // Limpa todos os veículos existentes
        \App\Models\Veiculo::query()->delete();

        // Cria 10 veículos com motorista e 5 sem
        \App\Models\Veiculo::factory()->count(10)->create();
        \App\Models\Veiculo::factory()->count(5)->semMotorista()->create();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/veiculos');

        $response->assertStatus(200)
            ->assertJsonCount(15); // 10 com motorista + 5 sem
    }

    public function test_criar_veiculo()
    {
        $motorista = Motorista::first();

        $dados = [
            'placa' => 'ABC1D23',
            'modelo' => 'Gol',
            'marca' => 'Volkswagen',
            'ano' => 2020,
            'status' => 'disponivel',
            'quilometragem' => 15000.50,
            'motorista_id' => $motorista->id,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/veiculos', $dados);

        $response->assertStatus(201)
            ->assertJson($dados);
    }

    public function test_validacao_ao_criar_veiculo()
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/veiculos', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'placa', 'modelo', 'marca', 'ano', 'status', 'quilometragem',
            ]);
    }

    public function test_mostrar_veiculo()
    {
        $veiculo = Veiculo::first();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson("/api/veiculos/{$veiculo->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $veiculo->id]);
    }

    public function test_atualizar_veiculo()
    {
        $veiculo = Veiculo::first();
        $novoMotorista = Motorista::where('id', '!=', $veiculo->motorista_id)->first();

        $dadosAtualizados = [
            'placa' => $veiculo->placa,
            'modelo' => 'Modelo Atualizado',
            'marca' => 'Marca Atualizada',
            'ano' => $veiculo->ano,
            'status' => 'em_uso',
            'quilometragem' => 20000.50,
            'motorista_id' => $novoMotorista->id,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->putJson("/api/veiculos/{$veiculo->id}", $dadosAtualizados);

        $response->assertStatus(200);

        // Verifica cada campo individualmente para lidar com a diferença de tipos
        $responseData = $response->json();
        $this->assertEquals($dadosAtualizados['modelo'], $responseData['modelo']);
        $this->assertEquals($dadosAtualizados['marca'], $responseData['marca']);
        $this->assertEquals($dadosAtualizados['status'], $responseData['status']);
        $this->assertEquals($dadosAtualizados['motorista_id'], $responseData['motorista_id']);
        $this->assertEquals(
            number_format($dadosAtualizados['quilometragem'], 2, '.', ''),
            number_format($responseData['quilometragem'], 2, '.', '')
        );
    }

    public function test_deletar_veiculo()
    {
        $veiculo = Veiculo::first();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->deleteJson("/api/veiculos/{$veiculo->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('veiculos', ['id' => $veiculo->id]);
    }

    public function test_apenas_usuarios_autenticados_podem_gerenciar_veiculos()
    {
        $veiculo = Veiculo::first();

        // Testar listagem sem autenticação
        $this->getJson('/api/veiculos')->assertStatus(401);

        // Testar criação sem autenticação
        $this->postJson('/api/veiculos', [])->assertStatus(401);

        // Testar visualização sem autenticação
        $this->getJson("/api/veiculos/{$veiculo->id}")->assertStatus(401);

        // Testar atualização sem autenticação
        $this->putJson("/api/veiculos/{$veiculo->id}", [])->assertStatus(401);

        // Testar exclusão sem autenticação
        $this->deleteJson("/api/veiculos/{$veiculo->id}")->assertStatus(401);
    }
}

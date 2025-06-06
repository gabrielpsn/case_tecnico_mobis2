<?php

namespace Tests\Feature;

use App\Models\Manutencao;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManutencaoTest extends TestCase
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

    public function test_listar_manutencoes()
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/manutencoes');

        $response->assertStatus(200);
    }

    public function test_criar_manutencao()
    {
        $veiculo = Veiculo::first();

        $dados = [
            'veiculo_id' => $veiculo->id,
            'data_manutencao' => '2025-06-06',
            'descricao' => 'Troca de óleo e filtros',
            'tipo' => 'preventiva',
            'km' => 15000.50,
            'custo' => 350.90,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/manutencoes', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'veiculo_id' => $dados['veiculo_id'],
                'descricao' => $dados['descricao'],
                'tipo' => $dados['tipo'],
                'km' => $dados['km'],
                'custo' => $dados['custo'],
            ]);
    }

    public function test_validacao_ao_criar_manutencao()
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/manutencoes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'veiculo_id', 'data_manutencao', 'descricao', 'tipo', 'km', 'custo',
            ]);
    }

    public function test_mostrar_manutencao()
    {
        $manutencao = Manutencao::first();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson("/api/manutencoes/{$manutencao->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $manutencao->id]);
    }

    public function test_atualizar_manutencao()
    {
        $manutencao = Manutencao::first();
        $novoVeiculo = Veiculo::where('id', '!=', $manutencao->veiculo_id)->first();

        $dadosAtualizados = [
            'veiculo_id' => $novoVeiculo->id,
            'data_manutencao' => '2025-07-01',
            'descricao' => 'Descrição atualizada',
            'tipo' => 'corretiva',
            'km' => 20000.75,
            'custo' => 500.00,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->putJson("/api/manutencoes/{$manutencao->id}", $dadosAtualizados);

        $response->assertStatus(200);

        // Verifica cada campo individualmente
        $responseData = $response->json();
        $this->assertEquals($dadosAtualizados['veiculo_id'], $responseData['veiculo_id']);
        $this->assertEquals($dadosAtualizados['data_manutencao'], substr($responseData['data_manutencao'], 0, 10));
        $this->assertEquals($dadosAtualizados['descricao'], $responseData['descricao']);
        $this->assertEquals($dadosAtualizados['tipo'], $responseData['tipo']);
        $this->assertEquals((float) $dadosAtualizados['km'], (float) $responseData['km']);
        $this->assertEquals((float) $dadosAtualizados['custo'], (float) $responseData['custo']);
    }

    public function test_deletar_manutencao()
    {
        $manutencao = Manutencao::first();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->deleteJson("/api/manutencoes/{$manutencao->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('manutencoes', ['id' => $manutencao->id]);
    }

    public function test_listar_manutencoes_por_veiculo()
    {
        // Limpa as manutenções existentes
        Manutencao::truncate();

        // Cria um veículo
        $veiculo = Veiculo::factory()->create();

        // Cria 3 manutenções para o veículo
        Manutencao::factory()
            ->count(3)
            ->create(['veiculo_id' => $veiculo->id]);

        // Tenta acessar a rota
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson("/api/veiculos/{$veiculo->id}/manutencoes");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_apenas_usuarios_autenticados_podem_gerenciar_manutencoes()
    {
        $manutencao = Manutencao::first();

        // Testar listagem sem autenticação
        $this->getJson('/api/manutencoes')->assertStatus(401);

        // Testar criação sem autenticação
        $this->postJson('/api/manutencoes', [])->assertStatus(401);

        // Testar visualização sem autenticação
        $this->getJson("/api/manutencoes/{$manutencao->id}")->assertStatus(401);

        // Testar atualização sem autenticação
        $this->putJson("/api/manutencoes/{$manutencao->id}", [])->assertStatus(401);

        // Testar exclusão sem autenticação
        $this->deleteJson("/api/manutencoes/{$manutencao->id}")->assertStatus(401);
    }
}

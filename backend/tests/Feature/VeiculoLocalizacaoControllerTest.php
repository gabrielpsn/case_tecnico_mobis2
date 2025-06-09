<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veiculo;
use App\Models\VeiculoLocalizacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VeiculoLocalizacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    /** @test */
    public function it_can_store_veiculo_localizacao()
    {
        $veiculo = Veiculo::factory()->create();
        $response = $this->postJson('/api/telemetria/localizacao', [
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:30:00Z',
            'localizacao' => [
                'latitude' => -7.9357,
                'longitude' => -34.8730,
                'velocidade' => 72.5,
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'veiculoId' => (string) $veiculo->id,
                    'localizacao' => [
                        'latitude' => -7.9357,
                        'longitude' => -34.8730,
                        'velocidade' => 72.5,
                    ],
                ],
            ]);

        // Remove the assertDatabaseHas check for MongoDB
        $this->assertTrue(
            VeiculoLocalizacao::where('veiculoId', (string) $veiculo->id)->exists(),
            'The veiculo localizacao was not stored in MongoDB'
        );
    }

    /** @test */
    public function it_can_retrieve_veiculo_localizacoes()
    {
        // Clear any existing test data
        VeiculoLocalizacao::truncate();

        $veiculo = Veiculo::factory()->create();
        $otherVeiculo = Veiculo::factory()->create(); // Create another veiculo to test isolation

        // Create test data directly in MongoDB
        $localizacao1 = VeiculoLocalizacao::create([
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:30:00Z',
            'localizacao' => [
                'latitude' => -7.9357,
                'longitude' => -34.8730,
                'velocidade' => 72.5,
            ],
        ]);

        $localizacao2 = VeiculoLocalizacao::create([
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:35:00Z',
            'localizacao' => [
                'latitude' => -7.9400,
                'longitude' => -34.8800,
                'velocidade' => 65.0,
            ],
        ]);

        // Add a record for a different vehicle to ensure we're filtering correctly
        VeiculoLocalizacao::create([
            'veiculoId' => (string) $otherVeiculo->id,
            'timestamp' => '2025-05-21T15:40:00Z',
            'localizacao' => [
                'latitude' => -7.9450,
                'longitude' => -34.8850,
                'velocidade' => 60.0,
            ],
        ]);

        $response = $this->getJson('/api/telemetria/veiculos/'.$veiculo->id.'/localizacao');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'veiculoId',
                        'localizacao' => [
                            'latitude',
                            'longitude',
                            'velocidade',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'veiculoId' => (string) $veiculo->id,
                'localizacao' => [
                    'latitude' => -7.9357,
                    'longitude' => -34.8730,
                    'velocidade' => 72.5,
                ],
            ]);
    }
}

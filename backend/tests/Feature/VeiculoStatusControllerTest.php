<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Veiculo;
use App\Models\VeiculoStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VeiculoStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    /** @test */
    public function it_can_store_veiculo_status()
    {
        $veiculo = Veiculo::factory()->create();

        $response = $this->postJson('/api/telemetria/status', [
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:30:00Z',
            'status' => [
                'rpm' => 3500,
                'temperatura' => 90,
                'combustivel' => 65,
                'checkEngine' => false,
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'veiculoId' => (string) $veiculo->id,
                    'status' => [
                        'rpm' => 3500,
                        'temperatura' => 90,
                        'combustivel' => 65,
                        'checkEngine' => false,
                    ],
                ],
            ]);

        // Check if the status was stored in MongoDB
        $this->assertTrue(
            VeiculoStatus::where('veiculoId', (string) $veiculo->id)->exists(),
            'The veiculo status was not stored in MongoDB'
        );
    }

    /** @test */
    public function it_can_retrieve_veiculo_status()
    {
        $veiculo = Veiculo::factory()->create();

        // Clear any existing test data
        VeiculoStatus::truncate();

        // Create test data directly in MongoDB
        $status1 = VeiculoStatus::create([
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:30:00Z',
            'status' => [
                'rpm' => 3500,
                'temperatura' => 90,
                'combustivel' => 65,
                'checkEngine' => false,
            ],
        ]);

        $status2 = VeiculoStatus::create([
            'veiculoId' => (string) $veiculo->id,
            'timestamp' => '2025-05-21T15:35:00Z',
            'status' => [
                'rpm' => 3000,
                'temperatura' => 95,
                'combustivel' => 60,
                'checkEngine' => true,
            ],
        ]);

        $response = $this->getJson('/api/telemetria/veiculos/'.$veiculo->id.'/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'veiculoId',
                        'status' => [
                            'rpm',
                            'temperatura',
                            'combustivel',
                            'checkEngine',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'veiculoId' => (string) $veiculo->id,
                'status' => [
                    'rpm' => 3500,
                    'temperatura' => 90,
                    'combustivel' => 65,
                    'checkEngine' => false,
                ],
            ]);
    }
}

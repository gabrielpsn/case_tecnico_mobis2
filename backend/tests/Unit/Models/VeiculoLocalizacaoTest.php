<?php

namespace Tests\Unit\Models;

use App\Models\VeiculoLocalizacao;
use Tests\TestCase;

class VeiculoLocalizacaoTest extends TestCase
{
    /** @test */
    public function it_can_create_veiculo_localizacao()
    {
        $localizacao = VeiculoLocalizacao::create([
            'veiculoId' => 'SQL_ID_123',
            'timestamp' => '2025-05-21T15:30:00Z',
            'localizacao' => [
                'latitude' => -7.9357,
                'longitude' => -34.8730,
                'velocidade' => 72.5,
            ],
        ]);

        $this->assertInstanceOf(VeiculoLocalizacao::class, $localizacao);
        $this->assertEquals('SQL_ID_123', $localizacao->veiculoId);
        $this->assertEquals(-7.9357, $localizacao->localizacao['latitude']);
        $this->assertEquals(72.5, $localizacao->localizacao['velocidade']);
    }

    /** @test */
    public function it_casts_timestamp_to_datetime()
    {
        $localizacao = new VeiculoLocalizacao([
            'timestamp' => '2025-05-21T15:30:00Z',
        ]);

        $this->assertInstanceOf(\DateTimeInterface::class, $localizacao->timestamp);
    }
}

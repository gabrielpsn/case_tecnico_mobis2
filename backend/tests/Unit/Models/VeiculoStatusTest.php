<?php

namespace Tests\Unit\Models;

use App\Models\VeiculoStatus;
use Tests\TestCase;

class VeiculoStatusTest extends TestCase
{
    /** @test */
    public function it_can_create_veiculo_status()
    {
        $status = VeiculoStatus::create([
            'veiculoId' => 'SQL_ID_123',
            'timestamp' => '2025-05-21T15:30:00Z',
            'status' => [
                'rpm' => 3500,
                'temperatura' => 90,
                'combustivel' => 65,
                'checkEngine' => false,
            ],
        ]);

        $this->assertInstanceOf(VeiculoStatus::class, $status);
        $this->assertEquals('SQL_ID_123', $status->veiculoId);
        $this->assertEquals(3500, $status->status['rpm']);
        $this->assertEquals(90, $status->status['temperatura']);
        $this->assertEquals(65, $status->status['combustivel']);
        $this->assertFalse($status->status['checkEngine']);
    }

    /** @test */
    public function it_casts_timestamp_to_datetime()
    {
        $status = new VeiculoStatus([
            'timestamp' => '2025-05-21T15:30:00Z',
        ]);

        $this->assertInstanceOf(\DateTimeInterface::class, $status->timestamp);
    }
}

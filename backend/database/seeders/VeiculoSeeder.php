<?php

namespace Database\Seeders;

use App\Models\Motorista;
use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class VeiculoSeeder extends Seeder
{
    public function run(): void
    {
        // Criar 10 veículos com motoristas aleatórios
        Veiculo::factory()
            ->count(10)
            ->create();

        // Criar alguns veículos sem motorista
        Veiculo::factory()
            ->count(5)
            ->create(['motorista_id' => null]);
    }
}

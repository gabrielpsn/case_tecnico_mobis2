<?php

namespace Database\Seeders;

use App\Models\Manutencao;
use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class ManutencaoSeeder extends Seeder
{
    public function run(): void
    {
        $veiculos = Veiculo::all();

        foreach ($veiculos as $veiculo) {
            // 1-3 manutenções por veículo
            Manutencao::factory()
                ->count(rand(1, 3))
                ->create(['veiculo_id' => $veiculo->id]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Veiculo;
use App\Models\VeiculoLocalizacao;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class VeiculoLocalizacaoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $veiculos = Veiculo::all();

        if ($veiculos->isEmpty()) {
            $this->command->info('Nenhum veículo encontrado. Execute o VeiculoSeeder primeiro.');

            return;
        }

        $this->command->info('Criando localizações para veículos...');
        $bar = $this->command->getOutput()->createProgressBar($veiculos->count() * 10);

        foreach ($veiculos as $veiculo) {
            // Cria 10 localizações para cada veículo
            for ($i = 0; $i < 10; $i++) {
                VeiculoLocalizacao::create([
                    'veiculoId' => $veiculo->id,
                    'timestamp' => now()->subHours(rand(1, 720)), // Até 30 dias atrás
                    'localizacao' => [
                        'latitude' => $faker->latitude(-23.7, -23.4),
                        'longitude' => $faker->longitude(-46.8, -46.4),
                        'velocidade' => $faker->randomFloat(2, 0, 120),
                        'endereco' => $faker->address,
                    ],
                ]);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Localizações dos veículos criadas com sucesso!');
    }
}

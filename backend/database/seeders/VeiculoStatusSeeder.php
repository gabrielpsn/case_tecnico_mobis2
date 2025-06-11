<?php

namespace Database\Seeders;

use App\Models\Veiculo;
use App\Models\VeiculoStatus;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class VeiculoStatusSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $veiculos = Veiculo::all();

        if ($veiculos->isEmpty()) {
            $this->command->info('Nenhum veículo encontrado. Execute o VeiculoSeeder primeiro.');

            return;
        }

        $statusPossiveis = [
            'em_movimento', 'parado', 'em_manutencao', 'desligado', 'em_espera',
        ];

        $this->command->info('Criando status para veículos...');
        $bar = $this->command->getOutput()->createProgressBar($veiculos->count() * 5);

        foreach ($veiculos as $veiculo) {
            // Cria 5 status para cada veículo
            for ($i = 0; $i < 5; $i++) {
                $status = $faker->randomElement($statusPossiveis);

                VeiculoStatus::create([
                    'veiculoId' => $veiculo->id,
                    'timestamp' => now()->subHours(rand(1, 720)), // Até 30 dias atrás
                    'status' => [
                        'status' => $status,
                        'codigo' => $this->getCodigoStatus($status),
                        'descricao' => $this->getDescricaoStatus($status),
                        'nivel_bateria' => $faker->numberBetween(10, 100),
                        'nivel_combustivel' => $faker->numberBetween(5, 100),
                        'quilometragem' => $faker->numberBetween(1000, 200000),
                        'mensagem' => $faker->sentence(6),
                    ],
                ]);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Status dos veículos criados com sucesso!');
    }

    private function getCodigoStatus($status): string
    {
        $codigos = [
            'em_movimento' => 'MOV',
            'parado' => 'STP',
            'em_manutencao' => 'MNT',
            'desligado' => 'OFF',
            'em_espera' => 'WAT',
        ];

        return $codigos[$status] ?? 'UNK';
    }

    private function getDescricaoStatus($status): string
    {
        $descricoes = [
            'em_movimento' => 'Veículo em movimento',
            'parado' => 'Veículo parado',
            'em_manutencao' => 'Veículo em manutenção',
            'desligado' => 'Veículo desligado',
            'em_espera' => 'Aguardando próxima viagem',
        ];

        return $descricoes[$status] ?? 'Status desconhecido';
    }
}

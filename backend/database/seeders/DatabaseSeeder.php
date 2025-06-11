<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->command->info('Iniciando seeders...');

            // Seeders principais (MySQL)
            $this->call([
                UserSeeder::class,
                MotoristaSeeder::class,
                VeiculoSeeder::class,
                ManutencaoSeeder::class,
            ]);

            // Seeders do MongoDB
            $this->call([
                VeiculoLocalizacaoSeeder::class,
                VeiculoStatusSeeder::class,
            ]);

            DB::commit();
            $this->command->info('Todos os seeders foram executados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Erro ao executar seeders: '.$e->getMessage());
            Log::error('Erro ao executar seeders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

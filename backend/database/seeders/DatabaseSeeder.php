<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Depois os usuários (que dependem das funções)
            UserSeeder::class,
            // Em seguida, os dados de negócio
            MotoristaSeeder::class,
            VeiculoSeeder::class,
            ManutencaoSeeder::class,
        ]);
    }
}

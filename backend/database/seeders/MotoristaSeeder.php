<?php

namespace Database\Seeders;

use App\Models\Motorista;
use Illuminate\Database\Seeder;

class MotoristaSeeder extends Seeder
{
    public function run(): void
    {
        Motorista::factory(10)->create();
    }
}

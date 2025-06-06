<?php

namespace Database\Factories;

use App\Enums\TipoManutencao;
use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManutencaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'veiculo_id' => Veiculo::factory(),
            'data_manutencao' => $this->faker->date(),
            'descricao' => $this->faker->sentence(),
            'tipo' => $this->faker->randomElement(TipoManutencao::cases())->value,
            'km' => $this->faker->randomFloat(2, 0, 500000),
            'custo' => $this->faker->randomFloat(2, 50, 5000),
        ];
    }
}

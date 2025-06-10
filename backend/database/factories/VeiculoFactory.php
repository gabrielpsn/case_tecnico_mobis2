<?php

namespace Database\Factories;

use App\Models\Motorista;
use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeiculoFactory extends Factory
{
    protected $model = Veiculo::class;

    public function definition(): array
    {
        return [
            'placa' => strtoupper($this->faker->bothify('???####')),
            'modelo' => $this->faker->word,
            'marca' => $this->faker->company,
            'ano' => $this->faker->numberBetween(2000, date('Y')),
            'status' => $this->faker->randomElement(['disponivel', 'em_uso', 'manutencao']),
            'quilometragem' => $this->faker->randomFloat(2, 0, 200000),
            'motorista_id' => Motorista::factory()->create()->id,
            'chassi' => $this->faker->bothify('###########'),
            'cor' => $this->faker->colorName,
            'observacoes' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function semMotorista(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'motorista_id' => null,
            ];
        });
    }
}

<?php

namespace Database\Factories;

use App\Models\Motorista;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotoristaFactory extends Factory
{
    protected $model = Motorista::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->numerify('###########'),
            'data_nascimento' => $this->faker->date(),
            'categoria_cnh' => $this->faker->randomElement(['A', 'B', 'AB', 'C', 'D', 'E']),
            'telefone' => $this->faker->phoneNumber,
        ];
    }
}

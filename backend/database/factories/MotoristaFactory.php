<?php

namespace Database\Factories;

use App\Models\Motorista;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotoristaFactory extends Factory
{
    protected $model = Motorista::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->name,
            'cpf' => $this->gerarCpfValido($this->faker),
            'data_nascimento' => $this->faker->date(),
            'categoria_cnh' => $this->faker->randomElement(['A', 'B', 'AB', 'C', 'D', 'E']),
            'telefone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'cnh' => $this->faker->numerify('###########'),
            'validade_cnh' => $this->faker->date(),
        ];
    }

    /**
     * Gera um CPF válido para testes
     */
    private function gerarCpfValido($faker): string
    {
        $n1 = $faker->numberBetween(0, 9);
        $n2 = $faker->numberBetween(0, 9);
        $n3 = $faker->numberBetween(0, 9);
        $n4 = $faker->numberBetween(0, 9);
        $n5 = $faker->numberBetween(0, 9);
        $n6 = $faker->numberBetween(0, 9);
        $n7 = $faker->numberBetween(0, 9);
        $n8 = $faker->numberBetween(0, 9);
        $n9 = $faker->numberBetween(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) {
            $d2 = 0;
        }

        return "$n1$n2$n3.$n4$n5$n6.$n7$n8$n9-$d1$d2";
    }
}

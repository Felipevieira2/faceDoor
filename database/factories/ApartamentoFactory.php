<?php

namespace Database\Factories;

use App\Models\Apartamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartamentoFactory extends Factory
{
    /**
     * O modelo associado a esta factory.
     *
     * @var string
     */
    protected $model = Apartamento::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'numero' => $this->faker->numberBetween(1, 100),
            'torre_id' => $this->faker->numberBetween(1, 10),
            'bloco' => $this->faker->randomElement(['A', 'B', 'C']),
            'ocupado' => $this->faker->boolean,
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Torre;
use Illuminate\Database\Eloquent\Factories\Factory;

class TorreFactory extends Factory
{
    /**
     * O modelo associado a esta factory.
     *
     * @var string
     */
    protected $model = Torre::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tenant_id' => $this->faker->numberBetween(1, 10),
            'nome' => $this->faker->name,
            'condominio_id' => $this->faker->numberBetween(1, 10),
            'descricao' => $this->faker->sentence,
            'numero_andares' => $this->faker->numberBetween(1, 10),
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitanteFactory extends Factory
{
    protected $model = Visitante::class;

    public function definition()
    {
        return [
          
            'ativo' => $this->faker->boolean,
            'data_validade_inicio' => $this->faker->dateTime,
            'data_validade_fim' => $this->faker->dateTime,
            'recorrente' => $this->faker->boolean,
            'dias_semana' => $this->faker->randomElement(['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']),
            'horario_inicio' => $this->faker->time,
            'horario_fim' => $this->faker->time,
        ];
    }
} 
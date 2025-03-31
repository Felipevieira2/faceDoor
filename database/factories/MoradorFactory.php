<?php

namespace Database\Factories;

use App\Models\Morador;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoradorFactory extends Factory
{
    protected $model = Morador::class;

    public function definition()
    {
        return [
            
            'data_inicio' => $this->faker->date,
            'data_fim' => $this->faker->date,
            'ativo' => $this->faker->boolean,
        ];
    }
} 
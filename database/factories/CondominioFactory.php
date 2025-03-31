<?php

namespace Database\Factories;

use App\Models\Condominio;
use Illuminate\Database\Eloquent\Factories\Factory;

class CondominioFactory extends Factory
{
    /**
     * O modelo associado a esta factory.
     *
     * @var string
     */
    protected $model = Condominio::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->company,
            'endereco' => $this->faker->address,
            'cep' => $this->faker->postcode,
            'cidade' => $this->faker->city,
            'estado' => $this->faker->stateAbbr,
            'telefone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
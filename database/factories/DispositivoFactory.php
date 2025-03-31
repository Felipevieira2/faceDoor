<?php

namespace Database\Factories;

use App\Models\Dispositivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispositivoFactory extends Factory
{
    /**
     * O modelo associado a esta factory.
     *
     * @var string
     */
    protected $model = Dispositivo::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        // 'nome',
        // 'identificador_unico',
        // 'condominio_id',
        // 'torre_id',
        // 'localizacao',
        // 'ativo',
        // 'fabricante',
        // 'username',
        // 'password',
        // 'ip',
        return [
            'tenant_id' => $this->faker->numberBetween(1, 10),
            'nome' => $this->faker->name,
            'localizacao' => $this->faker->address,
            'identificador_unico' => $this->faker->unique()->uuid,
            'fabricante' => $this->faker->randomElement(['controlid', 'intelbras']),
            'username' => $this->faker->username,
            'password' => $this->faker->password,
            'ip' => $this->faker->ipv4,
            // 'condominio_id' => $this->faker->numberBetween(1, 10),
            // 'torre_id' => $this->faker->numberBetween(1, 10),
            'ativo' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 
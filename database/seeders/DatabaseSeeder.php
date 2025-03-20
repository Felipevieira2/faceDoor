<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Condominio;
use Illuminate\Database\Seeder;
// use App\Models\Condominio;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Criar condomínio primeiro
        $condominio = Condominio::create([
            'nome' => 'Condomínio 1',
            'endereco' => 'Rua Exemplo, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '00000-000'
        ]);

        // Armazenar o tenant_id em uma variável global
        app()->instance('tenant_id', $condominio->id);

        // Chamar os seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TorreSeeder::class,
            // MoradorSeeder::class,
            // VisitanteSeeder::class,
            // DispositivoSeeder::class,
        ]);
    }
}

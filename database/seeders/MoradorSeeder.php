<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Apartamento;
use App\Models\Dispositivo;
use App\Models\Administrador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MoradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $parameters = [])
    {
        $tenant_id = app()->get('tenant_id');

        // 1. Criar usuário Administrador
      

        // Criar morador e visitante para teste
        Morador::factory()->create([
            'tenant_id' => $tenant_id,
            'user_id' => 1,
            'apartamento_id' => 1,
        ]);



    }
} 
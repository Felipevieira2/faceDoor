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

class DispositivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $parameters = [])
    {
        $tenant_id = app()->get('tenant_id');

        // 1. Criar usuário Administrador
        Dispositivo::factory()->create([
            'fabricante' => 'controlid',
            'identificador_unico' => '4408801109214683',
            'condominio_id' => '1',
            'ativo' => true,
            'tenant_id' => $tenant_id,
        ]);


    }
} 
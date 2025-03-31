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

class ApartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $parameters = [])
    {
        $tenant_id = app()->get('tenant_id');

        // 1. Criar usuário Administrador
        Apartamento::create([
            'torre_id' => '1',
            'numero' => '101',
        ]);


    }
} 
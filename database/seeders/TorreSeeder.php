<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Torre;

class TorreSeeder extends Seeder
{
    public function run()
    {
        $tenant_id = app()->get('tenant_id');

        Torre::create([
            'tenant_id' => $tenant_id,
            'condominio_id' => $tenant_id,
            'nome' => 'Torre A',
            'numero_andares' => 10
        ]);

        Torre::create([
            'tenant_id' => $tenant_id,
            'condominio_id' => $tenant_id,
            'nome' => 'Torre B',
            'numero_andares' => 10
        ]);
    }
} 
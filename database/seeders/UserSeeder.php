<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Administrador;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Torre;
use App\Models\Apartamento;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $parameters = [])
    {
        $tenant_id = app()->get('tenant_id');

        // 1. Criar usuário Administrador
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant_id,
        ]);
        
        // Atribuir papel de administrador
        $adminUser->assignRole('administrador');
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetar cache das permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // Criar permissões
        // // Permissões para condomínios
        // Permission::create(['name' => 'view condominios']);
        // Permission::create(['name' => 'create condominios']);
        // Permission::create(['name' => 'edit condominios']);
        // Permission::create(['name' => 'delete condominios']);

        // // Permissões para torres
        // Permission::create(['name' => 'view torres']);
        // Permission::create(['name' => 'create torres']);
        // Permission::create(['name' => 'edit torres']);
        // Permission::create(['name' => 'delete torres']);

        // // Permissões para apartamentos
        // Permission::create(['name' => 'view apartamentos']);
        // Permission::create(['name' => 'create apartamentos']);
        // Permission::create(['name' => 'edit apartamentos']);
        // Permission::create(['name' => 'delete apartamentos']);

        // // Permissões para dispositivos
        // Permission::create(['name' => 'view dispositivos']);
        // Permission::create(['name' => 'create dispositivos']);
        // Permission::create(['name' => 'edit dispositivos']);
        // Permission::create(['name' => 'delete dispositivos']);

        // // Permissões para visitantes
        // Permission::create(['name' => 'view visitantes']);
        // Permission::create(['name' => 'create visitantes']);
        // Permission::create(['name' => 'edit visitantes']);
        // Permission::create(['name' => 'delete visitantes']);

        // // Permissões para usuários
        // Permission::create(['name' => 'view users']);
        // Permission::create(['name' => 'create users']);
        // Permission::create(['name' => 'edit users']);
        // Permission::create(['name' => 'delete users']);

        // Criar papéis e atribuir permissões
        
        // Papel de Administrador (anteriormente Zelador)
        $adminRole = Role::create(['name' => 'administrador']);
        // $adminRole->givePermissionTo([
        //     'view condominios', 'create condominios', 'edit condominios', 'delete condominios',
        //     'view torres', 'create torres', 'edit torres', 'delete torres',
        //     'view apartamentos', 'create apartamentos', 'edit apartamentos', 'delete apartamentos',
        //     'view dispositivos', 'create dispositivos', 'edit dispositivos', 'delete dispositivos',
        //     'view visitantes', 'create visitantes', 'edit visitantes', 'delete visitantes',
        //     'view users', 'create users', 'edit users', 'delete users',
        // ]);

        // Papel de Morador
        $moradorRole = Role::create(['name' => 'morador']);
        // $moradorRole->givePermissionTo([
        //     'view condominios',
        //     'view torres',
        //     'view apartamentos',
        //     'view visitantes', 'create visitantes', 'edit visitantes',
        // ]);

        // Papel de Visitante
        // $visitanteRole = Role::create(['name' => 'visitante']);
        // $visitanteRole->givePermissionTo([
        //     'view condominios',
        // ]);
    }
}

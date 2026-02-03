<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir todos los permisos
        $allPermissions = [
            'save user',
            'edit user',
            'actions user',
            'inactive user',
            'consult user',
            'delete user',
            'save company',
            'edit company',
            'actions company',
            'delete company',
            'consult company',
            'save equipment',
            'edit equipment',
            'actions equipment',
            'delete equipment',
            'consult equipment',
            'save report',
            'edit report',
            'actions report',
            'delete report',
            'consult report',
        ];

        // Crear permisos (si no existen)
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Rol Supervisor: tiene limitaciones en crear empresas, usuarios y tipo de componente
        $supervisorPermissions = [
            'consult user',
            'consult company',
            'save equipment',
            'edit equipment',
            'actions equipment',
            'delete equipment',
            'consult equipment',
            'save report',
            'edit report',
            'actions report',
            'delete report',
            'consult report',
        ];
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisorRole->syncPermissions($supervisorPermissions);

        // Rol Administrador: todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);

        // Rol Cliente: Sólo visualiza información de su empresa
        $clientePermissions = [
            'consult company',
            'consult equipment',
            'consult report',
        ];
        $clienteRole = Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);
        $clienteRole->syncPermissions($clientePermissions);
    }
}

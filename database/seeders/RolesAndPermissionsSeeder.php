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
        // Quitar roles obsoletos (solo usamos Administrador, Supervisor, Cliente)
        Role::whereIn('name', ['admin', 'user'])->delete();

        // Definir todos los permisos
        $allPermissions = [
            'consult role',
            'edit role',
            'save role',
            'save user',
            'edit user',
            'detalle user',
            'inactive user',
            'consult user',
            'delete user',
            'save company',
            'edit company',
            'detalle company',
            'delete company',
            'consult company',
            'save equipment',
            'edit equipment',
            'detalle equipment',
            'delete equipment',
            'consult equipment',
            'save component type',
            'edit component type',
            'detalle component type',
            'delete component type',
            'consult component type',
            'save report',
            'edit report',
            'detalle report',
            'delete report',
            'consult report',
            'download pdf report',
            'send email report',
            'consult indicators',
        ];

        // Crear permisos (si no existen)
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Rol Supervisor: tiene limitaciones en crear empresas, usuarios y tipo de componente
        $supervisorPermissions = [
            'consult role',
            'edit role',
            'save role',
            'consult user',
            'consult company',
            'save equipment',
            'edit equipment',
            'detalle equipment',
            'delete equipment',
            'consult equipment',
            'save component type',
            'edit component type',
            'detalle component type',
            'delete component type',
            'consult component type',
            'save report',
            'edit report',
            'detalle report',
            'delete report',
            'consult report',
            'download pdf report',
            'send email report',
            'consult indicators',
        ];
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisorRole->syncPermissions($supervisorPermissions);

        // Rol Administrador: todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);

        // Rol Cliente: Sólo Inicio, Informes y Equipos (sin Tipo Componente, Usuario, Empresa)
        $clientePermissions = [
            'consult equipment',
            'detalle equipment',
            'consult report',
            'detalle report',
            'consult indicators',
        ];
        $clienteRole = Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);
        $clienteRole->syncPermissions($clientePermissions);

        // Eliminar permisos obsoletos "actions X"
        Permission::whereIn('name', [
            'actions user', 'actions company', 'actions equipment', 'actions component type', 'actions report',
        ])->delete();
    }
}

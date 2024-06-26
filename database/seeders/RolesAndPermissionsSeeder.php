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
        // Definir permisos
        $permissionsAdm = [
            'save user',
            'edit user',
            'actions user',
            'inactive user',
            'consult user',
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
        ];

        // Crear permisos
        foreach ($permissionsAdm as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->syncPermissions($permissionsAdm);


        $permissionsUser= [
            'consult user',
            'consult company',
            'consult equipment',
        ];


        $userRole = Role::create(['name' => 'user']);
        $userRole->syncPermissions($permissionsUser);




    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Elimina el rol Administrador de la base de datos (el admin del sistema no es un rol asignable).
     */
    public function up(): void
    {
        $role = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($role) {
            $role->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $allPermissions = [
            'save user', 'edit user', 'actions user', 'inactive user', 'consult user', 'delete user',
            'save company', 'edit company', 'actions company', 'delete company', 'consult company',
            'save equipment', 'edit equipment', 'actions equipment', 'delete equipment', 'consult equipment',
            'save report', 'edit report', 'actions report', 'delete report', 'consult report',
        ];
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);
    }
};

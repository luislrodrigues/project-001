<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renumera los IDs de roles para que queden consecutivos (1, 2) sin saltos.
     */
    public function up(): void
    {
        $roles = DB::table('roles')
            ->where('guard_name', 'web')
            ->orderBy('id')
            ->get(['id', 'name']);

        if ($roles->count() < 2) {
            return;
        }

        $idMap = []; // old_id => new_id (1, 2, ...)
        $newId = 1;
        foreach ($roles as $role) {
            $idMap[$role->id] = $newId++;
        }

        // Si ya están bien numerados (1, 2, ...) no hacemos nada
        $needsFix = false;
        foreach ($idMap as $oldId => $newId) {
            if ($oldId !== $newId) {
                $needsFix = true;
                break;
            }
        }
        if (!$needsFix) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        $tempPrefix = 9000;

        // 1. Roles: id actual → temporal (2→9001, 3→9002)
        foreach ($idMap as $oldId => $newId) {
            $tempId = $tempPrefix + $newId;
            DB::table('roles')->where('id', $oldId)->update(['id' => $tempId]);
        }

        // 2. Pivotes: apuntar a IDs temporales
        foreach ($idMap as $oldId => $newId) {
            $tempId = $tempPrefix + $newId;
            DB::table('model_has_roles')->where('role_id', $oldId)->update(['role_id' => $tempId]);
            DB::table('role_has_permissions')->where('role_id', $oldId)->update(['role_id' => $tempId]);
        }

        // 3. Roles: temporal → definitivo (9001→1, 9002→2)
        foreach ($idMap as $oldId => $newId) {
            $tempId = $tempPrefix + $newId;
            DB::table('roles')->where('id', $tempId)->update(['id' => $newId]);
        }

        // 4. Pivotes: temporal → definitivo
        foreach ($idMap as $oldId => $newId) {
            $tempId = $tempPrefix + $newId;
            DB::table('model_has_roles')->where('role_id', $tempId)->update(['role_id' => $newId]);
            DB::table('role_has_permissions')->where('role_id', $tempId)->update(['role_id' => $newId]);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir la renumeración
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Imagen Térmica" estaba modelada como campo de medición numérico
 * (guardaba un número sin significado y se imprimía como parámetro).
 * Las fotos ahora viven en report_images, así que el campo se retira.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('component_type_fields')) {
            return;
        }

        $fieldIds = DB::table('component_type_fields')
            ->where('name', 'Imagen Térmica')
            ->where('scope', 'measurement')
            ->pluck('id')
            ->all();

        if ($fieldIds === []) {
            return;
        }

        if (Schema::hasTable('report_component_values')) {
            DB::table('report_component_values')
                ->whereIn('component_type_field_id', $fieldIds)
                ->delete();
        }

        DB::table('component_type_fields')
            ->whereIn('id', $fieldIds)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('component_type_fields')) {
            return;
        }

        DB::table('component_type_fields')
            ->where('name', 'Imagen Térmica')
            ->where('scope', 'measurement')
            ->update([
                'deleted_at' => null,
                'updated_at' => now(),
            ]);
    }
};

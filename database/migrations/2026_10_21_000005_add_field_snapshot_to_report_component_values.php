<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda una copia del nombre/unidad/orden del campo al momento de la medición.
 * Así un informe histórico conserva la etiqueta con la que se capturó el dato
 * aunque después se renombre el campo o se cambie su unidad.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('report_component_values')) {
            return;
        }

        Schema::table('report_component_values', function (Blueprint $table) {
            if (!Schema::hasColumn('report_component_values', 'field_name')) {
                $table->string('field_name', 255)->nullable()->after('component_type_field_id');
            }
            if (!Schema::hasColumn('report_component_values', 'field_unit')) {
                $table->string('field_unit', 50)->nullable()->after('field_name');
            }
            if (!Schema::hasColumn('report_component_values', 'field_order')) {
                $table->unsignedInteger('field_order')->nullable()->after('field_unit');
            }
        });

        DB::table('report_component_values')
            ->join('component_type_fields', 'component_type_fields.id', '=', 'report_component_values.component_type_field_id')
            ->whereNull('report_component_values.field_name')
            ->update([
                'report_component_values.field_name' => DB::raw('component_type_fields.name'),
                'report_component_values.field_unit' => DB::raw('component_type_fields.unit'),
                'report_component_values.field_order' => DB::raw('component_type_fields.display_order'),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('report_component_values')) {
            return;
        }

        Schema::table('report_component_values', function (Blueprint $table) {
            foreach (['field_name', 'field_unit', 'field_order'] as $column) {
                if (Schema::hasColumn('report_component_values', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

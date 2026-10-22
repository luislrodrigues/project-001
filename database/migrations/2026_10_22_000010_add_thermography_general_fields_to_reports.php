<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Termografía: Emisividad, Distancia y Temperatura Ambiente pasan de ser
 * valores por punto a datos generales del informe.
 *
 * Además, Condición / Mecanismo / Componente en Falla / Prioridad dejan de
 * digitarse en termografía (se derivan del punto más crítico), por lo que se
 * relajan a NULL. Vibración sigue enviándolos, así que no le afecta.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reports')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'emissivity')) {
                $table->decimal('emissivity', 4, 3)->nullable()->after('measurement_date');
            }
            if (!Schema::hasColumn('reports', 'distance_m')) {
                $table->decimal('distance_m', 6, 2)->nullable()->after('emissivity');
            }
            if (!Schema::hasColumn('reports', 'ambient_temp')) {
                $table->decimal('ambient_temp', 5, 2)->nullable()->after('distance_m');
            }
        });

        foreach (['condition_id', 'mechanism_fail_id', 'component_fail_id', 'priority_id'] as $column) {
            DB::statement("ALTER TABLE `reports` MODIFY `{$column}` BIGINT UNSIGNED NULL");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('reports')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            foreach (['ambient_temp', 'distance_m', 'emissivity'] as $column) {
                if (Schema::hasColumn('reports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Solo se restauran los NOT NULL si no quedaron filas con valores vacíos.
        foreach (['condition_id', 'mechanism_fail_id', 'component_fail_id', 'priority_id'] as $column) {
            $hasNulls = DB::table('reports')->whereNull($column)->exists();
            if (!$hasNulls) {
                DB::statement("ALTER TABLE `reports` MODIFY `{$column}` BIGINT UNSIGNED NOT NULL");
            }
        }
    }
};

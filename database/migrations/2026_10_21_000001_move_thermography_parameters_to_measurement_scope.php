<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Emisividad y Distancia son parámetros de cada medición (varían por captura),
 * no valores fijos del componente. Se copian los valores existentes a los
 * reportes históricos antes de cambiar el scope.
 */
return new class extends Migration
{
    private const PARAMETERS = ['Emisividad', 'Distancia'];

    public function up(): void
    {
        if (!Schema::hasTable('component_type_fields') || !Schema::hasColumn('component_type_fields', 'scope')) {
            return;
        }

        $typeIds = DB::table('component_types')
            ->where('report_type', 'thermography')
            ->whereNull('deleted_at')
            ->pluck('id');

        if ($typeIds->isEmpty()) {
            return;
        }

        $fieldIds = DB::table('component_type_fields')
            ->whereIn('component_type_id', $typeIds)
            ->whereIn('name', self::PARAMETERS)
            ->whereNull('deleted_at')
            ->pluck('id');

        if ($fieldIds->isEmpty()) {
            return;
        }

        $now = now();

        // Copiar el valor fijo del componente al reporte para no perder lo ya impreso en PDFs históricos
        $componentValues = DB::table('component_type_values')
            ->whereIn('component_type_field_id', $fieldIds)
            ->whereNull('deleted_at')
            ->get();

        foreach ($componentValues as $value) {
            $equipmentId = DB::table('components')->where('id', $value->component_id)->value('equipment_id');
            if (!$equipmentId) {
                continue;
            }

            $reportIds = DB::table('reports')
                ->where('equipment_id', $equipmentId)
                ->where('report_type', 'thermography')
                ->whereNull('deleted_at')
                ->pluck('id');

            foreach ($reportIds as $reportId) {
                DB::table('report_component_values')->insertOrIgnore([
                    'report_id' => $reportId,
                    'component_id' => $value->component_id,
                    'component_type_field_id' => $value->component_type_field_id,
                    'value' => $value->value,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        DB::table('component_type_fields')
            ->whereIn('id', $fieldIds)
            ->update(['scope' => 'measurement']);

        // Orden de parámetros alineado al informe: ΔT, imagen, emisividad, distancia, ambiente
        $orders = [
            'Delta T°' => 1,
            'Imagen Térmica' => 2,
            'Emisividad' => 3,
            'Distancia' => 4,
            'Temp. Ambiente' => 5,
        ];

        foreach ($orders as $name => $order) {
            DB::table('component_type_fields')
                ->whereIn('component_type_id', $typeIds)
                ->where('name', $name)
                ->whereNull('deleted_at')
                ->update(['display_order' => $order]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('component_type_fields', 'scope')) {
            return;
        }

        $typeIds = DB::table('component_types')
            ->where('report_type', 'thermography')
            ->pluck('id');

        if ($typeIds->isEmpty()) {
            return;
        }

        $fieldIds = DB::table('component_type_fields')
            ->whereIn('component_type_id', $typeIds)
            ->whereIn('name', self::PARAMETERS)
            ->pluck('id');

        if ($fieldIds->isEmpty()) {
            return;
        }

        DB::table('report_component_values')->whereIn('component_type_field_id', $fieldIds)->delete();

        DB::table('component_type_fields')
            ->whereIn('id', $fieldIds)
            ->update(['scope' => 'component']);
    }
};

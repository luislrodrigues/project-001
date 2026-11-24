<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Repara los datos de informes que quedaron apuntando a componentes
 * eliminados: al guardar un equipo se borraban y recreaban sus componentes,
 * así que imágenes, valores de medición, diagnóstico y recomendaciones
 * quedaban asociados a ids que ya no existen.
 *
 * Aquí se reasocian al componente activo equivalente del mismo equipo
 * (mismo tipo y marca; se prefiere el mismo orden).
 */
return new class extends Migration
{
    public function up(): void
    {
        $reports = DB::table('reports')->orderBy('id')->get(['id', 'equipment_id', 'diagnosis', 'recommendations']);

        foreach ($reports as $report) {
            $referenced = $this->referencedComponentIds(
                (int) $report->id,
                $report->diagnosis,
                $report->recommendations
            );

            if (empty($referenced)) {
                continue;
            }

            $map = $this->buildComponentMap((int) $report->equipment_id, $referenced);
            if (empty($map)) {
                continue;
            }

            $this->relinkImages((int) $report->id, $map);
            $this->relinkMeasurementValues((int) $report->id, $map);
            $this->relinkJsonField((int) $report->id, 'diagnosis', $report->diagnosis, $map);
            $this->relinkJsonField((int) $report->id, 'recommendations', $report->recommendations, $map);
        }
    }

    /**
     * Ids de componente referenciados por un informe (imágenes, mediciones y
     * claves de diagnóstico/recomendaciones).
     *
     * @return array<int, int>
     */
    private function referencedComponentIds(int $reportId, $diagnosis, $recommendations): array
    {
        $ids = collect()
            ->merge(DB::table('report_images')->where('report_id', $reportId)->pluck('component_id'))
            ->merge(DB::table('report_component_values')->where('report_id', $reportId)->pluck('component_id'));

        foreach ([$diagnosis, $recommendations] as $raw) {
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (!is_array($decoded) || $decoded === []) {
                continue;
            }
            if (array_keys($decoded) === range(0, count($decoded) - 1)) {
                continue;
            }
            $ids = $ids->merge(array_keys($decoded));
        }

        return $ids
            ->filter(fn ($id) => $id !== null && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $referencedIds
     * @return array<int, int> id eliminado => id activo
     */
    private function buildComponentMap(int $equipmentId, array $referencedIds): array
    {
        $active = DB::table('components')
            ->where('equipment_id', $equipmentId)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'component_type_id', 'brand_id', 'display_order']);

        if ($active->isEmpty() || empty($referencedIds)) {
            return [];
        }

        $activeIds = $active->pluck('id')->map(fn ($id) => (int) $id)->all();

        $orphans = DB::table('components')
            ->where('equipment_id', $equipmentId)
            ->whereIn('id', $referencedIds)
            ->whereNotNull('deleted_at')
            ->whereNotIn('id', $activeIds)
            ->get(['id', 'component_type_id', 'brand_id', 'display_order']);

        $map = [];

        foreach ($orphans as $old) {
            $candidates = $active->filter(fn ($c) => (int) $c->component_type_id === (int) $old->component_type_id
                && (int) $c->brand_id === (int) $old->brand_id);

            if ($candidates->isEmpty()) {
                continue;
            }

            $match = $candidates->firstWhere('display_order', $old->display_order) ?? $candidates->first();

            if ($match && (int) $match->id !== (int) $old->id) {
                $map[(int) $old->id] = (int) $match->id;
            }
        }

        return $map;
    }

    /**
     * @param  array<int, int>  $map
     */
    private function relinkImages(int $reportId, array $map): void
    {
        $images = DB::table('report_images')
            ->where('report_id', $reportId)
            ->whereIn('component_id', array_keys($map))
            ->get(['id', 'component_id', 'path']);

        foreach ($images as $image) {
            $targetId = $map[(int) $image->component_id] ?? null;
            if ($targetId === null) {
                continue;
            }

            $duplicate = DB::table('report_images')
                ->where('report_id', $reportId)
                ->where('component_id', $targetId)
                ->where('path', $image->path)
                ->where('id', '!=', $image->id)
                ->exists();

            if ($duplicate) {
                DB::table('report_images')->where('id', $image->id)->delete();
                continue;
            }

            DB::table('report_images')->where('id', $image->id)->update([
                'component_id' => $targetId,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $map
     */
    private function relinkMeasurementValues(int $reportId, array $map): void
    {
        $values = DB::table('report_component_values')
            ->where('report_id', $reportId)
            ->whereIn('component_id', array_keys($map))
            ->get(['id', 'component_id', 'component_type_field_id', 'value', 'status']);

        foreach ($values as $value) {
            $targetId = $map[(int) $value->component_id] ?? null;
            if ($targetId === null) {
                continue;
            }

            $existing = DB::table('report_component_values')
                ->where('report_id', $reportId)
                ->where('component_id', $targetId)
                ->where('component_type_field_id', $value->component_type_field_id)
                ->where('id', '!=', $value->id)
                ->first();

            if ($existing) {
                if (trim((string) $existing->value) === '' && trim((string) $value->value) !== '') {
                    DB::table('report_component_values')->where('id', $existing->id)->update([
                        'value' => $value->value,
                        'updated_at' => now(),
                    ]);
                }

                DB::table('report_component_values')->where('id', $value->id)->delete();
                continue;
            }

            DB::table('report_component_values')->where('id', $value->id)->update([
                'component_id' => $targetId,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $map
     */
    private function relinkJsonField(int $reportId, string $column, $raw, array $map): void
    {
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        if (!is_array($decoded) || $decoded === []) {
            return;
        }

        // Los arrays posicionales se resuelven por orden del equipo, no por id.
        if (array_keys($decoded) === range(0, count($decoded) - 1)) {
            return;
        }

        $changed = false;
        $result = $decoded;

        foreach ($decoded as $key => $value) {
            $targetId = $map[(int) $key] ?? null;
            if ($targetId === null) {
                continue;
            }

            $changed = true;
            unset($result[$key]);

            $targetKey = (string) $targetId;
            if (!array_key_exists($targetKey, $result)) {
                $result[$targetKey] = $value;
                continue;
            }

            $result[$targetKey] = $column === 'recommendations'
                ? $this->mergeRecommendations($result[$targetKey], $value)
                : $this->mergeDiagnosis($result[$targetKey], $value);
        }

        if (!$changed) {
            return;
        }

        DB::table('reports')->where('id', $reportId)->update([
            $column => json_encode($result),
            'updated_at' => now(),
        ]);
    }

    private function mergeDiagnosis($current, $incoming)
    {
        $currentText = is_array($current) ? (string) ($current['text'] ?? '') : (string) $current;
        $incomingText = is_array($incoming) ? (string) ($incoming['text'] ?? '') : (string) $incoming;

        if (trim($currentText) !== '' || trim($incomingText) === '') {
            return $current;
        }

        return $incoming;
    }

    private function mergeRecommendations($current, $incoming): array
    {
        $current = is_array($current) ? $current : [];
        $incoming = is_array($incoming) ? $incoming : [];

        return array_values(array_unique(array_merge($current, $incoming)));
    }

    public function down(): void
    {
        // Reparación de datos: no se revierte.
    }
};

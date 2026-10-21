<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Company;
use App\Models\Equipment;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Indicadores estadísticos a partir de los datos existentes (reportes, equipos, empresas, etc.)
 * Opcional: ?company_id=X para indicadores filtrados por empresa.
 * Opcional: ?process_id=X para indicadores filtrados por proceso (equipos del proceso).
 * Opcional: ?measurement_date_start=AAAA-MM-DD&measurement_date_end=AAAA-MM-DD para filtrar por rango de fecha de medición.
 */
class IndicadoresController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->filled('company_id') ? $request->integer('company_id') : null;
        $processId = $request->filled('process_id') ? $request->integer('process_id') : null;

        // Si el usuario es Cliente, solo puede ver indicadores de su empresa
        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $companyId = auth()->user()->company_id;
        }

        $startRaw = (string) $request->input('measurement_date_start', '');
        $endRaw = (string) $request->input('measurement_date_end', '');
        $start = ($startRaw !== '' && strtotime($startRaw) !== false) ? date('Y-m-d', strtotime($startRaw)) : null;
        $end = ($endRaw !== '' && strtotime($endRaw) !== false) ? date('Y-m-d', strtotime($endRaw)) : null;

        $reportsBase = Report::whereNull('reports.deleted_at');
        if ($start && $end) {
            $reportsBase->whereBetween('reports.measurement_date', [$start, $end]);
        } elseif ($start) {
            $reportsBase->whereDate('reports.measurement_date', '>=', $start);
        } elseif ($end) {
            $reportsBase->whereDate('reports.measurement_date', '<=', $end);
        }
        if ($companyId || $processId) {
            $reportsBase->whereHas('equipment', function ($q) use ($companyId, $processId) {
                $q->whereNull('deleted_at');
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                if ($processId) {
                    $q->where('process_id', $processId);
                }
            });
        }

        $equipmentBase = Equipment::whereNull('equipment.deleted_at')->where('equipment.status', 'active');
        if ($companyId) {
            $equipmentBase->where('company_id', $companyId);
        }
        if ($processId) {
            $equipmentBase->where('process_id', $processId);
        }

        // Totales
        $totales = [
            'empresas' => $companyId ? 1 : Company::whereNull('deleted_at')->where('status', 'active')->count(),
            'equipos' => (clone $equipmentBase)->count(),
            'reportes' => (clone $reportsBase)->count(),
            'reportes_este_mes' => (clone $reportsBase)->whereMonth('reports.created_at', now()->month)->whereYear('reports.created_at', now()->year)->count(),
            'reportes_este_ano' => (clone $reportsBase)->whereYear('reports.created_at', now()->year)->count(),
        ];

        // Reportes por mes: con rango de fechas de medición se agrupa por esa fecha;
        // sin rango se mantiene la tendencia de los últimos 12 meses (por fecha de creación).
        $reportesPorMesQuery = (clone $reportsBase);
        if ($start || $end) {
            $reportesPorMesQuery
                ->selectRaw('YEAR(reports.measurement_date) as anio, MONTH(reports.measurement_date) as mes, COUNT(*) as total')
                ->whereNotNull('reports.measurement_date');
        } else {
            $reportesPorMesQuery
                ->selectRaw('YEAR(reports.created_at) as anio, MONTH(reports.created_at) as mes, COUNT(*) as total')
                ->where('reports.created_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        $reportesPorMes = $reportesPorMesQuery
            ->groupBy('anio', 'mes')
            ->orderBy('anio')->orderBy('mes')
            ->get()
            ->map(fn ($r) => [
                'anio' => (int) $r->anio,
                'mes' => (int) $r->mes,
                'label' => \Carbon\Carbon::createFromDate($r->anio, $r->mes, 1)->translatedFormat('M Y'),
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por condición
        $reportesPorCondicion = (clone $reportsBase)
            ->leftJoin('conditions', 'reports.condition_id', '=', 'conditions.id')
            ->selectRaw('reports.condition_id, conditions.name as condicion_nombre, conditions.color, COUNT(*) as total')
            ->groupBy('reports.condition_id', 'conditions.name', 'conditions.color')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'condition_id' => $r->condition_id,
                'nombre' => $r->condicion_nombre ?? 'Sin condición',
                'color' => $r->color,
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por empresa (vía equipo); si hay company_id solo devolvemos esa empresa
        $reportesPorEmpresaQuery = (clone $reportsBase)
            ->join('equipment', 'reports.equipment_id', '=', 'equipment.id')
            ->whereNull('equipment.deleted_at')
            ->leftJoin('companies', 'equipment.company_id', '=', 'companies.id')
            ->selectRaw('companies.id as company_id, companies.name as empresa_nombre, COUNT(*) as total')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('total');
        if ($companyId) {
            $reportesPorEmpresaQuery->where('companies.id', $companyId);
        }
        $reportesPorEmpresa = $reportesPorEmpresaQuery->get()
            ->map(fn ($r) => [
                'company_id' => $r->company_id,
                'nombre' => $r->empresa_nombre ?? 'Sin empresa',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por analista
        $reportesPorAnalista = (clone $reportsBase)
            ->leftJoin('users', 'reports.analist_id', '=', 'users.id')
            ->selectRaw('reports.analist_id, users.name as analista_nombre, COUNT(*) as total')
            ->groupBy('reports.analist_id', 'users.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'analist_id' => $r->analist_id,
                'nombre' => $r->analista_nombre ?? 'Sin asignar',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por proceso (vía equipo)
        $reportesPorProceso = (clone $reportsBase)
            ->join('equipment', 'reports.equipment_id', '=', 'equipment.id')
            ->whereNull('equipment.deleted_at')
            ->leftJoin('processes', 'equipment.process_id', '=', 'processes.id')
            ->selectRaw('processes.id as process_id, processes.name as proceso_nombre, COUNT(*) as total')
            ->groupBy('processes.id', 'processes.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'process_id' => $r->process_id,
                'nombre' => $r->proceso_nombre ?? 'Sin proceso',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por tipo de equipo (vía equipo)
        $reportesPorTipoEquipo = (clone $reportsBase)
            ->join('equipment', 'reports.equipment_id', '=', 'equipment.id')
            ->whereNull('equipment.deleted_at')
            ->leftJoin('equipment_types', 'equipment.equipment_type_id', '=', 'equipment_types.id')
            ->selectRaw('equipment_types.id as type_id, equipment_types.name as tipo_nombre, COUNT(*) as total')
            ->groupBy('equipment_types.id', 'equipment_types.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'type_id' => $r->type_id,
                'nombre' => $r->tipo_nombre ?? 'Sin tipo',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por ubicación (vía equipo)
        $reportesPorUbicacion = (clone $reportsBase)
            ->join('equipment', 'reports.equipment_id', '=', 'equipment.id')
            ->whereNull('equipment.deleted_at')
            ->leftJoin('ubications', 'equipment.ubication_id', '=', 'ubications.id')
            ->selectRaw('ubications.id as ubication_id, ubications.name as ubicacion_nombre, COUNT(*) as total')
            ->groupBy('ubications.id', 'ubications.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'ubication_id' => $r->ubication_id,
                'nombre' => $r->ubicacion_nombre ?? 'Sin ubicación',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por mecanismo de falla
        $reportesPorMecanismoFalla = (clone $reportsBase)
            ->leftJoin('mechanism_fails', 'reports.mechanism_fail_id', '=', 'mechanism_fails.id')
            ->selectRaw('reports.mechanism_fail_id, mechanism_fails.name as mecanismo_nombre, COUNT(*) as total')
            ->groupBy('reports.mechanism_fail_id', 'mechanism_fails.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'mechanism_fail_id' => $r->mechanism_fail_id,
                'nombre' => $r->mecanismo_nombre ?? 'Sin asignar',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Reportes por componente de falla
        $reportesPorComponenteFalla = (clone $reportsBase)
            ->leftJoin('component_fails', 'reports.component_fail_id', '=', 'component_fails.id')
            ->selectRaw('reports.component_fail_id, component_fails.name as componente_nombre, COUNT(*) as total')
            ->groupBy('reports.component_fail_id', 'component_fails.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'component_fail_id' => $r->component_fail_id,
                'nombre' => $r->componente_nombre ?? 'Sin asignar',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Top equipos con más reportes
        $topEquiposReportes = (clone $reportsBase)
            ->join('equipment', 'reports.equipment_id', '=', 'equipment.id')
            ->whereNull('equipment.deleted_at')
            ->selectRaw('equipment.id as equipment_id, equipment.name as equipo_nombre, equipment.code as equipo_codigo, COUNT(*) as total')
            ->groupBy('equipment.id', 'equipment.name', 'equipment.code')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'equipment_id' => $r->equipment_id,
                'nombre' => $r->equipo_nombre ?? $r->equipo_codigo ?? 'Sin nombre',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // Equipos por empresa (top); si hay company_id solo esa empresa
        $equiposPorEmpresaQuery = (clone $equipmentBase)
            ->leftJoin('companies', 'equipment.company_id', '=', 'companies.id')
            ->whereNull('companies.deleted_at')
            ->selectRaw('companies.id as company_id, companies.name as empresa_nombre, COUNT(*) as total')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('total')
            ->limit($companyId ? 1 : 10);
        if ($companyId) {
            $equiposPorEmpresaQuery->where('companies.id', $companyId);
        }
        $equiposPorEmpresa = $equiposPorEmpresaQuery->get()
            ->map(fn ($r) => [
                'company_id' => $r->company_id,
                'nombre' => $r->empresa_nombre ?? 'Sin empresa',
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        $data = [
            'company_id' => $companyId,
            'company_selected' => $companyId ? Company::find($companyId)?->only(['id', 'name']) : null,
            'measurement_date_start' => $start,
            'measurement_date_end' => $end,
            'totales' => $totales,
            'reportes_por_mes' => $reportesPorMes,
            'reportes_por_condicion' => $reportesPorCondicion,
            'reportes_por_empresa' => $reportesPorEmpresa,
            'reportes_por_analista' => $reportesPorAnalista,
            'reportes_por_proceso' => $reportesPorProceso,
            'reportes_por_tipo_equipo' => $reportesPorTipoEquipo,
            'reportes_por_ubicacion' => $reportesPorUbicacion,
            'reportes_por_mecanismo_falla' => $reportesPorMecanismoFalla,
            'reportes_por_componente_falla' => $reportesPorComponenteFalla,
            'top_equipos_reportes' => $topEquiposReportes,
            'equipos_por_empresa' => $equiposPorEmpresa,
        ];

        return ApiResponse::success($data);
    }
}

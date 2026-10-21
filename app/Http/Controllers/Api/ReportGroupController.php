<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreReportGroupRequest;
use App\Http\Requests\Api\UpdateReportGroupRequest;
use App\Http\Resources\ReportGroupResource;
use App\Models\Report;
use App\Models\ReportGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDF;

class ReportGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = ReportGroup::with(['company', 'process', 'ubication'])
            ->withCount('reports')
            ->whereNull('deleted_at');

        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $reportType = $request->get('report_type');
        if (is_string($reportType) && $reportType !== '') {
            $query->where('report_type', $reportType);
        }

        $companyIds = array_values(array_filter(array_map('intval', (array) $request->input('company_id'))));
        if ($companyIds) {
            $query->whereIn('company_id', $companyIds);
        }

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('period', 'like', "%{$q}%");
            });
        }

        $groups = $query->orderByDesc('service_date')
            ->orderByDesc('id')
            ->paginate($request->get('per_page', 10));

        return ApiResponse::success(ReportGroupResource::collection($groups));
    }

    public function store(StoreReportGroupRequest $request): JsonResponse
    {
        $reportIds = $this->validateReportsBelongToGroup($request);

        $group = DB::transaction(function () use ($request, $reportIds) {
            $group = ReportGroup::create([
                'code' => $request->filled('code') ? trim((string) $request->code) : null,
                'report_type' => $request->report_type,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'title' => $request->title,
                'subject' => $request->subject,
                'period' => $request->period,
                'service_date' => $request->service_date,
                'summary' => $request->summary,
                'responsible_name' => $request->responsible_name,
                'responsible_position' => $request->responsible_position,
                'responsible_certification' => $request->responsible_certification,
                'status' => 'active',
            ]);

            Report::whereIn('id', $reportIds)->update(['report_group_id' => $group->id]);

            return $group;
        });

        return ApiResponse::success(
            new ReportGroupResource($group->load(['company', 'process', 'ubication', 'reports.equipment', 'reports.condition', 'reports.measurementValues.field'])),
            'Se guardó el informe correctamente',
            201
        );
    }

    public function show($id): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $group = ReportGroup::with([
            'company',
            'process',
            'ubication',
            'reports.equipment',
            'reports.condition',
            'reports.measurementValues.field',
        ])->whereNull('deleted_at')->findOrFail($id);

        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id && (int) $group->company_id !== (int) auth()->user()->company_id) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        return ApiResponse::success(new ReportGroupResource($group));
    }

    public function update(UpdateReportGroupRequest $request, $id): JsonResponse
    {
        $group = ReportGroup::whereNull('deleted_at')->findOrFail($id);

        $reportIds = $this->validateReportsBelongToGroup($request);

        DB::transaction(function () use ($group, $request, $reportIds) {
            $group->update([
                'code' => $request->filled('code') ? trim((string) $request->code) : $group->code,
                'report_type' => $request->report_type,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'title' => $request->title,
                'subject' => $request->subject,
                'period' => $request->period,
                'service_date' => $request->service_date,
                'summary' => $request->summary,
                'responsible_name' => $request->responsible_name,
                'responsible_position' => $request->responsible_position,
                'responsible_certification' => $request->responsible_certification,
            ]);

            // Desvincular los que salieron del informe y vincular los seleccionados
            Report::where('report_group_id', $group->id)
                ->whereNotIn('id', $reportIds)
                ->update(['report_group_id' => null]);

            Report::whereIn('id', $reportIds)->update(['report_group_id' => $group->id]);
        });

        return ApiResponse::success(
            new ReportGroupResource($group->fresh()->load(['company', 'process', 'ubication', 'reports.equipment', 'reports.condition', 'reports.measurementValues.field'])),
            'Se actualizó el informe correctamente'
        );
    }

    public function destroy($id): JsonResponse
    {
        if (!auth()->user()->can('delete report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $group = ReportGroup::whereNull('deleted_at')->findOrFail($id);

        DB::transaction(function () use ($group) {
            Report::where('report_group_id', $group->id)->update(['report_group_id' => null]);
            $group->status = 'inactive';
            $group->save();
            $group->delete();
        });

        return ApiResponse::success(null, 'El informe se eliminó correctamente');
    }

    public function downloadPdf($id)
    {
        if (!auth()->user()->can('download pdf report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        ini_set('pcre.backtrack_limit', '10000000');
        ini_set('pcre.recursion_limit', '10000000');

        try {
            $group = ReportGroup::with([
                'company',
                'process',
                'ubication',
                'reports' => function ($query) {
                    $query->whereNull('deleted_at')->orderBy('measurement_date')->orderBy('id');
                },
                'reports.equipment.components.componentType',
                'reports.equipment.components.brand',
                'reports.equipment.components.fields',
                'reports.equipment.components.fieldValues',
                'reports.equipment.company',
                'reports.equipment.equipmentType',
                'reports.equipment.ubication',
                'reports.equipment.process',
                'reports.condition',
                'reports.priority',
                'reports.mechanismFail',
                'reports.componentFail',
                'reports.analist',
                'reports.measurementValues.field',
                'reports.images',
            ])->whereNull('deleted_at')->findOrFail($id);

            if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id && (int) $group->company_id !== (int) auth()->user()->company_id) {
                abort(403, 'No tienes los permisos requeridos.');
            }

            if ($group->reports->isEmpty()) {
                return ApiResponse::error('El informe no tiene puntos asociados.', 422);
            }

            $companyIds = $group->reports->pluck('equipment.company_id')->filter()->unique();

            if ($companyIds->count() > 1) {
                return ApiResponse::error('Los puntos del informe pertenecen a más de una empresa.', 422);
            }

            $points = $group->reports->map(function (Report $report) {
                return array_merge(
                    ReportController::pdfViewData($report),
                    ['report' => $report]
                );
            });

            $html = view('reports.thermography-group-pdf', [
                'group' => $group,
                'points' => $points,
            ])->render();

            $html = utf8_sanitize_html($html);

            $pdf = PDF::loadHTML($html, [
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 28,
                'margin_header' => 0,
                'margin_footer' => 10,
                'ignore_invalid_utf8' => true,
            ]);

            $datePrefix = $group->service_date?->format('Ymd') ?? '';
            $subject = $this->sanitizeFilenamePart($group->subject ?? $group->title ?? '');
            $company = $this->sanitizeFilenamePart($group->company?->name ?? '');

            $filename = trim(implode(' ', array_filter([
                $datePrefix,
                'Informe Termografía',
                $group->display_code,
                $subject !== '' ? $subject : $company,
            ]))) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al generar el PDF', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Valida que todos los reportes existan, sean del mismo tipo y empresa del informe.
     *
     * @return array<int, int>
     */
    private function validateReportsBelongToGroup(Request $request): array
    {
        $reportIds = array_values(array_unique(array_map('intval', (array) $request->input('report_ids', []))));

        if (empty($reportIds)) {
            throw ValidationException::withMessages([
                'report_ids' => 'Selecciona al menos un punto para el informe.',
            ]);
        }

        $reports = Report::with('equipment:id,company_id')
            ->whereIn('id', $reportIds)
            ->whereNull('deleted_at')
            ->get(['id', 'equipment_id', 'report_type']);

        if ($reports->count() !== count($reportIds)) {
            throw ValidationException::withMessages([
                'report_ids' => 'Uno o más reportes seleccionados no existen.',
            ]);
        }

        $invalid = $reports->first(function (Report $report) use ($request) {
            return (int) ($report->equipment?->company_id) !== (int) $request->company_id
                || $report->report_type !== $request->report_type;
        });

        if ($invalid) {
            throw ValidationException::withMessages([
                'report_ids' => 'Todos los puntos deben pertenecer a la empresa y al tipo de informe seleccionado.',
            ]);
        }

        return $reportIds;
    }

    private function sanitizeFilenamePart(string $value): string
    {
        $clean = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $value);
        return trim(preg_replace('/\s+/', ' ', $clean ?? ''));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Http\Requests\Api\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Condition;
use App\Models\ComponentFail;
use App\Models\Equipment;
use App\Models\MechanismFail;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function getConditions(): JsonResponse
    {
        $conditions = Condition::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $conditions]);
    }

    public function getMechanismFails(): JsonResponse
    {
        $fails = MechanismFail::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $fails]);
    }

    public function getComponentFails(): JsonResponse
    {
        $fails = ComponentFail::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $fails]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = Report::with(['equipment.company', 'equipment.equipmentType', 'equipment.ubication', 'equipment.process', 'equipment.components', 'condition', 'mechanismFail', 'componentFail', 'analist'])
            ->whereNull('deleted_at');

        if ($request->has('equipment_id') && $request->equipment_id !== '') {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->has('condition_id') && $request->condition_id !== '') {
            $query->where('condition_id', $request->condition_id);
        }

        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('company_id') && $request->company_id !== '') {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));

        return ReportResource::collection($reports);
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        try {
            $imagePath1 = null;
            $imagePath2 = null;

            if ($request->hasFile('image_1')) {
                $imagePath1 = $request->file('image_1')->store('images', 'public');
            }

            if ($request->hasFile('image_2')) {
                $imagePath2 = $request->file('image_2')->store('images', 'public');
            }

            $data = [
                'equipment_id' => $request->equipment_id,
                'measurement_date' => $request->measurement_date,
                'condition_id' => $request->condition_id,
                'priority' => $request->priority,
                'mechanism_fail_id' => $request->mechanism_fail_id,
                'component_fail_id' => $request->component_fail_id,
                'ot_created' => $request->ot_created,
                'status_ot' => $request->status_ot,
                'execution_date' => $request->execution_date,
                'verification_recommendation' => $request->verification_recommendation,
                'analist_id' => $request->analist_id,
            ];

            if ($request->has('diagnosis')) {
                $data['diagnosis'] = json_encode($request->diagnosis);
            }

            if ($request->has('recommendations')) {
                $data['recommendations'] = json_encode($request->recommendations);
            }

            if ($imagePath1) {
                $data['image_1'] = $imagePath1;
            }

            if ($imagePath2) {
                $data['image_2'] = $imagePath2;
            }

            $report = Report::create($data);

            return response()->json([
                'message' => 'Se guardó el reporte correctamente',
                'data' => new ReportResource($report->load(['equipment.components', 'condition', 'mechanismFail', 'componentFail', 'analist']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar el reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Report $report): JsonResponse
    {
        return response()->json([
            'data' => new ReportResource($report->load(['equipment.components.componentType', 'equipment.components.brand', 'condition', 'mechanismFail', 'componentFail', 'analist']))
        ]);
    }

    public function update(UpdateReportRequest $request, Report $report): JsonResponse
    {
        try {
            $imagePath1 = null;
            $imagePath2 = null;

            // Manejar imágenes si vienen en el request
            if ($request->hasFile('image_1')) {
                // Eliminar imagen anterior si existe
                if ($report->image_1) {
                    Storage::disk('public')->delete($report->image_1);
                }
                $imagePath1 = $request->file('image_1')->store('images', 'public');
            }

            if ($request->hasFile('image_2')) {
                // Eliminar imagen anterior si existe
                if ($report->image_2) {
                    Storage::disk('public')->delete($report->image_2);
                }
                $imagePath2 = $request->file('image_2')->store('images', 'public');
            }

            $data = [
                'equipment_id' => $request->equipment_id,
                'measurement_date' => $request->measurement_date,
                'condition_id' => $request->condition_id,
                'priority' => $request->priority,
                'mechanism_fail_id' => $request->mechanism_fail_id,
                'component_fail_id' => $request->component_fail_id,
                'ot_created' => $request->ot_created,
                'status_ot' => $request->status_ot,
                'execution_date' => $request->execution_date,
                'verification_recommendation' => $request->verification_recommendation,
                'analist_id' => $request->analist_id,
            ];

            if ($request->has('diagnosis')) {
                $data['diagnosis'] = is_array($request->diagnosis) ? json_encode($request->diagnosis) : $request->diagnosis;
            }

            if ($request->has('recommendations')) {
                $data['recommendations'] = is_array($request->recommendations) ? json_encode($request->recommendations) : $request->recommendations;
            }

            if ($imagePath1) {
                $data['image_1'] = $imagePath1;
            }

            if ($imagePath2) {
                $data['image_2'] = $imagePath2;
            }

            $report->update($data);

            return response()->json([
                'message' => 'Se actualizó el reporte correctamente',
                'data' => new ReportResource($report->load(['equipment.components', 'condition', 'mechanismFail', 'componentFail', 'analist']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Report $report): JsonResponse
    {
        if (!auth()->user()->can('delete report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $report->status = 'inactive';
            $report->save();
            $report->delete();

            return response()->json([
                'message' => 'El reporte se eliminó correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf($id): JsonResponse
    {
        // Placeholder - implementar cuando se migre la generación de PDF
        return response()->json(['message' => 'En desarrollo']);
    }

    public function export(): JsonResponse
    {
        // Placeholder - implementar cuando se migre la exportación
        return response()->json(['message' => 'En desarrollo']);
    }

    public function sendEmail($id): JsonResponse
    {
        // Placeholder - implementar cuando se migre el envío de emails
        return response()->json(['message' => 'En desarrollo']);
    }
}

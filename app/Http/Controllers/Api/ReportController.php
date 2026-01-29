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
use PDF;
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

            // Procesar imágenes por componente
            $componentImages = [];
            if ($request->has('component_images_structure')) {
                $structure = json_decode($request->input('component_images_structure'), true);
                if (is_array($structure)) {
                    foreach ($structure as $componentId => $imageKeys) {
                        $componentImages[$componentId] = [];
                        foreach ($imageKeys as $imageKey) {
                            if ($request->hasFile($imageKey)) {
                                $file = $request->file($imageKey);
                                if ($file && $file->isValid()) {
                                    $path = $file->store('images', 'public');
                                    $componentImages[$componentId][] = $path;
                                }
                            }
                        }
                    }
                }
            }


            // Guardar imágenes de componentes en el campo diagnosis como parte de un objeto extendido
            // O mejor, almacenarlas en un campo separado si existe
            if (!empty($componentImages)) {
                // Almacenar en diagnosis junto con el texto del diagnóstico
                $diagnosisData = $request->has('diagnosis') ? $request->diagnosis : [];
                foreach ($componentImages as $componentId => $imagePaths) {
                    if (!isset($diagnosisData[$componentId])) {
                        $diagnosisData[$componentId] = [];
                    }
                    if (!is_array($diagnosisData[$componentId])) {
                        $diagnosisData[$componentId] = ['text' => $diagnosisData[$componentId], 'images' => []];
                    } else if (!isset($diagnosisData[$componentId]['images'])) {
                        $diagnosisData[$componentId] = ['text' => $diagnosisData[$componentId]['text'] ?? '', 'images' => []];
                    }
                    $diagnosisData[$componentId]['images'] = $imagePaths;
                }
                $data['diagnosis'] = json_encode($diagnosisData);
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

    public function show($id): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $report = Report::with([
            'equipment.components.componentType',
            'equipment.components.brand',
            'equipment.company',
            'equipment.equipmentType',
            'equipment.ubication',
            'equipment.process',
            'condition',
            'mechanismFail',
            'componentFail',
            'analist'
        ])
        ->whereNull('deleted_at')
        ->findOrFail($id);

        return response()->json([
            'data' => new ReportResource($report)
        ]);
    }

    public function update(UpdateReportRequest $request, $id): JsonResponse
    {
        if (!auth()->user()->can('edit report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $report = Report::whereNull('deleted_at')->findOrFail($id);

        try {
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

            // Procesar imágenes por componente
            $componentImages = [];
            if ($request->has('component_images_structure')) {
                $structure = json_decode($request->input('component_images_structure'), true);
                if (is_array($structure)) {
                    foreach ($structure as $componentId => $imageKeys) {
                        $componentImages[$componentId] = [];
                        foreach ($imageKeys as $imageKey) {
                            if ($request->hasFile($imageKey)) {
                                $file = $request->file($imageKey);
                                if ($file && $file->isValid()) {
                                    $path = $file->store('images', 'public');
                                    $componentImages[$componentId][] = $path;
                                }
                            }
                        }
                    }
                }
            }

            // Manejar eliminación de imágenes
            $imagesToDelete = [];
            if ($request->has('images_to_delete')) {
                $imagesToDelete = json_decode($request->input('images_to_delete'), true);
                if (is_array($imagesToDelete)) {
                    foreach ($imagesToDelete as $componentId => $imageUrls) {
                        foreach ($imageUrls as $imageUrl) {
                            // Extraer la ruta del storage desde la URL completa
                            $path = str_replace(asset('storage/'), '', $imageUrl);
                            if ($path && Storage::disk('public')->exists($path)) {
                                Storage::disk('public')->delete($path);
                            }
                        }
                    }
                }
            }

            // Obtener diagnosis actual y actualizar con nuevas imágenes
            $currentDiagnosis = $report->diagnosis ? (is_string($report->diagnosis) ? json_decode($report->diagnosis, true) : $report->diagnosis) : [];
            $diagnosisData = $request->has('diagnosis') ? $request->diagnosis : $currentDiagnosis;

            // Actualizar diagnosis con nuevas imágenes y eliminar las marcadas
            foreach ($componentImages as $componentId => $newImagePaths) {
                $componentIdStr = (string)$componentId;
                if (!isset($diagnosisData[$componentIdStr])) {
                    $diagnosisData[$componentIdStr] = '';
                }
                
                // Obtener imágenes existentes
                $existingImages = [];
                if (isset($currentDiagnosis[$componentIdStr]) && is_array($currentDiagnosis[$componentIdStr]) && isset($currentDiagnosis[$componentIdStr]['images'])) {
                    $existingImages = $currentDiagnosis[$componentIdStr]['images'];
                } else if (isset($currentDiagnosis[$componentIdStr]) && is_array($currentDiagnosis[$componentIdStr])) {
                    $existingImages = $currentDiagnosis[$componentIdStr]['images'] ?? [];
                }

                // Eliminar imágenes marcadas para eliminar
                if (isset($imagesToDelete[$componentIdStr]) && is_array($imagesToDelete[$componentIdStr])) {
                    foreach ($imagesToDelete[$componentIdStr] as $imageUrlToDelete) {
                        $pathToDelete = str_replace(asset('storage/'), '', $imageUrlToDelete);
                        $existingImages = array_filter($existingImages, function($img) use ($pathToDelete) {
                            return $img !== $pathToDelete;
                        });
                    }
                }

                // Combinar imágenes existentes con nuevas
                $allImages = array_merge($existingImages, $newImagePaths);

                // Convertir a estructura con texto e imágenes
                $text = is_string($diagnosisData[$componentIdStr]) ? $diagnosisData[$componentIdStr] : ($diagnosisData[$componentIdStr]['text'] ?? '');
                $diagnosisData[$componentIdStr] = [
                    'text' => $text,
                    'images' => array_values($allImages)
                ];
            }

            // Actualizar diagnosis con estructura completa
            $data['diagnosis'] = json_encode($diagnosisData);


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

    public function destroy($id): JsonResponse
    {
        if (!auth()->user()->can('delete report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $report = Report::whereNull('deleted_at')->findOrFail($id);
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

    public function downloadPdf($id)
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $report = Report::with([
                'equipment.components.componentType',
                'equipment.components.brand',
                'equipment.components.fields.value',
                'equipment.company',
                'equipment.equipmentType',
                'equipment.ubication',
                'equipment.process',
                'condition',
                'mechanismFail',
                'componentFail',
                'analist'
            ])
            ->whereNull('deleted_at')
            ->findOrFail($id);

            // Procesar diagnóstico para el PDF
            $diagnosis = [];
            if ($report->diagnosis) {
                $decoded = is_string($report->diagnosis) ? json_decode($report->diagnosis, true) : $report->diagnosis;
                if (is_array($decoded)) {
                    // Si es un array indexado numéricamente, mapearlo a IDs de componentes
                    $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray && $report->equipment && $report->equipment->components) {
                        foreach ($decoded as $index => $data) {
                            if (isset($report->equipment->components[$index])) {
                                $componentId = $report->equipment->components[$index]->id;
                                if (is_array($data) && isset($data['text'])) {
                                    $diagnosis[$componentId] = $data['text'];
                                } else if (is_string($data)) {
                                    $diagnosis[$componentId] = $data;
                                }
                            }
                        }
                    } else {
                        // Es un objeto asociativo
                        foreach ($decoded as $componentId => $data) {
                            if (is_array($data) && isset($data['text'])) {
                                $diagnosis[$componentId] = $data['text'];
                            } else if (is_string($data)) {
                                $diagnosis[$componentId] = $data;
                            }
                        }
                    }
                }
            }

            // Procesar recomendaciones para el PDF
            $recommendations = [];
            if ($report->recommendations) {
                $decoded = is_string($report->recommendations) ? json_decode($report->recommendations, true) : $report->recommendations;
                if (is_array($decoded)) {
                    $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray && $report->equipment && $report->equipment->components) {
                        foreach ($decoded as $index => $data) {
                            if (isset($report->equipment->components[$index])) {
                                $componentId = $report->equipment->components[$index]->id;
                                $recommendations[$componentId] = is_array($data) ? $data : [];
                            }
                        }
                    } else {
                        $recommendations = $decoded;
                    }
                }
            }

            // Procesar imágenes por componente para el PDF
            $componentImages = [];
            if ($report->diagnosis) {
                $decoded = is_string($report->diagnosis) ? json_decode($report->diagnosis, true) : $report->diagnosis;
                if (is_array($decoded)) {
                    $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray && $report->equipment && $report->equipment->components) {
                        foreach ($decoded as $index => $data) {
                            if (isset($report->equipment->components[$index])) {
                                $componentId = $report->equipment->components[$index]->id;
                                if (is_array($data) && isset($data['images'])) {
                                    $componentImages[$componentId] = array_map(function ($path) {
                                        return self::imageToBase64ForPdf($path);
                                    }, $data['images']);
                                }
                            }
                        }
                    } else {
                        foreach ($decoded as $componentId => $data) {
                            if (is_array($data) && isset($data['images'])) {
                                $componentImages[$componentId] = array_map(function ($path) {
                                    return self::imageToBase64ForPdf($path);
                                }, $data['images']);
                            }
                        }
                    }
                }
            }

            $components = $report->equipment->components ?? [];

            $equipmentImage = null;
            if ($report->equipment && !empty($report->equipment->image)) {
                $equipmentImage = self::imageToBase64ForPdf($report->equipment->image);
            }

            $pdf = PDF::loadView('reports.report-pdf', [
                'reportData' => $report,
                'components' => $components,
                'diagnosis' => $diagnosis,
                'recommendations' => $recommendations,
                'componentImages' => $componentImages,
                'equipmentImage' => $equipmentImage,
            ], [], [
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_top'    => 28,
                'margin_header' => 0,
                'margin_footer' => 10,
            ]);

            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, 'reporte-' . $report->id . '.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar el PDF',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Convierte una imagen (ruta relativa en storage) a data URI base64 para que mPDF la muestre siempre en el PDF.
     */
    private static function imageToBase64ForPdf(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        $fullPath = storage_path('app/public/' . ltrim($path, '/'));
        if (!is_file($fullPath)) {
            return '';
        }
        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
    }
}

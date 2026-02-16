<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreReportRequest;
use App\Http\Requests\Api\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Condition;
use App\Models\ComponentFail;
use App\Models\Priority;
use App\Models\Equipment;
use App\Models\MechanismFail;
use App\Models\Report;
use App\Models\User;
use App\Support\ReportNumberConverter;
use PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function getConditions(): JsonResponse
    {
        $conditions = Condition::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'color', 'text_color')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($conditions);
    }

    public function getPriorities(): JsonResponse
    {
        $priorities = Priority::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'color', 'text_color')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($priorities);
    }

    public function getMechanismFails(): JsonResponse
    {
        $fails = MechanismFail::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($fails);
    }

    public function getComponentFails(): JsonResponse
    {
        $fails = ComponentFail::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($fails);
    }

    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = Report::with(['equipment.company', 'equipment.equipmentType', 'equipment.ubication', 'equipment.process', 'equipment.components', 'condition', 'priority', 'mechanismFail', 'componentFail', 'analist'])
            ->whereNull('deleted_at');

        // Filtrar por empresa si el usuario es Cliente
        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $query->whereHas('equipment', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }

        if ($request->has('equipment_id') && $request->equipment_id !== '') {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->has('condition_id') && $request->condition_id !== '') {
            $query->where('condition_id', $request->condition_id);
        }

        if ($request->has('priority_id') && $request->priority_id !== '') {
            $query->where('priority_id', $request->priority_id);
        }

        if ($request->has('company_id') && $request->company_id !== '') {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('process_id')) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('process_id', (int) $request->process_id);
            });
        }

        if ($request->filled('analist_id')) {
            $query->where('analist_id', (int) $request->analist_id);
        }

        if ($request->filled('consecutivo')) {
            $reportId = ReportNumberConverter::parseToId($request->input('consecutivo', ''));
            if ($reportId !== null) {
                $query->where('id', $reportId);
            }
        }

        $sortByRaw = $request->get('sort_by', 'id');
        $allowedSort = ['id', 'created_at', 'measurement_date'];
        $sortBy = (is_string($sortByRaw) && $sortByRaw !== '' && in_array($sortByRaw, $allowedSort, true))
            ? $sortByRaw
            : 'id';
        $sortOrderRaw = $request->get('sort_order', 'desc');
        $sortOrder = (is_string($sortOrderRaw) && in_array(strtolower($sortOrderRaw), ['asc', 'desc'], true))
            ? strtolower($sortOrderRaw)
            : 'desc';
        $reports = $query->orderBy($sortBy, $sortOrder)->paginate($request->get('per_page', 10));

        return ApiResponse::success(ReportResource::collection($reports));
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        try {
            $data = [
                'equipment_id' => $request->equipment_id,
                'measurement_date' => $request->measurement_date,
                'condition_id' => $request->condition_id,
                'priority_id' => $request->priority_id,
                'mechanism_fail_id' => $request->mechanism_fail_id,
                'component_fail_id' => $request->component_fail_id,
                'ot_created' => $request->ot_created,
                'status_ot' => $request->status_ot,
                'execution_date' => $request->execution_date,
                'general_recommendations' => self::normalizeGeneralRecommendations($request->general_recommendations ?? []),
                'analist_id' => $request->analist_id,
            ];

            if ($request->has('diagnosis')) {
                $data['diagnosis'] = is_array($request->diagnosis) ? $request->diagnosis : (json_decode($request->diagnosis, true) ?? []);
            }

            if ($request->has('recommendations')) {
                $data['recommendations'] = is_array($request->recommendations) ? $request->recommendations : (json_decode($request->recommendations, true) ?? []);
            }

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


            if (!empty($componentImages)) {
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
                $data['diagnosis'] = $diagnosisData;
            }

            $report = Report::create($data);

            return ApiResponse::success(
                new ReportResource($report->load(['equipment.components', 'condition', 'mechanismFail', 'componentFail', 'analist'])),
                'Se guardó el reporte correctamente',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al guardar el reporte', 500, ['detail' => [$e->getMessage()]]);
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
            'priority',
            'mechanismFail',
            'componentFail',
            'analist'
        ])
        ->whereNull('deleted_at')
        ->findOrFail($id);

        return ApiResponse::success(new ReportResource($report));
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
                'priority_id' => $request->priority_id,
                'mechanism_fail_id' => $request->mechanism_fail_id,
                'component_fail_id' => $request->component_fail_id,
                'ot_created' => $request->ot_created,
                'status_ot' => $request->status_ot,
                'execution_date' => $request->execution_date,
                'general_recommendations' => self::normalizeGeneralRecommendations($request->general_recommendations ?? []),
                'analist_id' => $request->analist_id,
            ];

            if ($request->has('diagnosis')) {
                $data['diagnosis'] = is_array($request->diagnosis) ? $request->diagnosis : (json_decode($request->diagnosis, true) ?? []);
            }

            if ($request->has('recommendations')) {
                $data['recommendations'] = is_array($request->recommendations) ? $request->recommendations : (json_decode($request->recommendations, true) ?? []);
            }

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

            $imagesToDelete = [];
            if ($request->has('images_to_delete')) {
                $imagesToDelete = json_decode($request->input('images_to_delete'), true);
                if (is_array($imagesToDelete)) {
                    foreach ($imagesToDelete as $componentId => $imageUrls) {
                        foreach ($imageUrls as $imageUrl) {
                            $path = str_replace(asset('storage/'), '', $imageUrl);
                            if ($path && Storage::disk('public')->exists($path)) {
                                Storage::disk('public')->delete($path);
                            }
                        }
                    }
                }
            }

            // Obtener diagnosis actual y actualizar con nuevas imágenes
            $currentDiagnosis = self::decodeJsonField($report->diagnosis ?? []);
            $diagnosisData = $request->has('diagnosis')
                ? (is_string($request->diagnosis) ? json_decode($request->diagnosis, true) : $request->diagnosis)
                : $currentDiagnosis;
            if (!is_array($diagnosisData)) {
                $diagnosisData = $currentDiagnosis;
            }

            // Normalizar URL o path a path relativo (ej: images/xxx.jpg) para comparar
            $normalizeImagePath = function ($urlOrPath) {
                $u = trim($urlOrPath);
                if (strpos($u, 'http') === 0 || strpos($u, '//') === 0) {
                    $u = preg_replace('#^https?://[^/]+/#', '', $u);
                    $u = preg_replace('#^storage/#', '', $u);
                }
                return $u;
            };

            // Aplicar imágenes a eliminar a diagnosisData para todos los componentes en imagesToDelete
            foreach ($imagesToDelete as $componentId => $urlsToDelete) {
                $componentIdStr = (string)$componentId;
                if (!is_array($urlsToDelete)) {
                    continue;
                }
                $existing = $diagnosisData[$componentIdStr] ?? [];
                $text = is_array($existing) ? ($existing['text'] ?? '') : (string)$existing;
                $existingImages = is_array($existing) && isset($existing['images']) ? $existing['images'] : [];
                $pathsToRemove = array_map($normalizeImagePath, $urlsToDelete);
                $existingImages = array_values(array_filter($existingImages, function ($img) use ($pathsToRemove, $normalizeImagePath) {
                    $imgPath = $normalizeImagePath($img);
                    return !in_array($imgPath, $pathsToRemove, true);
                }));
                $diagnosisData[$componentIdStr] = ['text' => $text, 'images' => $existingImages];
            }

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

                if (isset($imagesToDelete[$componentIdStr]) && is_array($imagesToDelete[$componentIdStr])) {
                    $pathsToDelete = array_map($normalizeImagePath, $imagesToDelete[$componentIdStr]);
                    $existingImages = array_values(array_filter($existingImages, function ($img) use ($pathsToDelete, $normalizeImagePath) {
                        return !in_array($normalizeImagePath($img), $pathsToDelete, true);
                    }));
                }

                // Combinar imágenes existentes con nuevas
                $allImages = array_merge($existingImages, $newImagePaths);

                $text = is_string($diagnosisData[$componentIdStr]) ? $diagnosisData[$componentIdStr] : ($diagnosisData[$componentIdStr]['text'] ?? '');
                $diagnosisData[$componentIdStr] = [
                    'text' => $text,
                    'images' => array_values($allImages)
                ];
            }

            $data['diagnosis'] = $diagnosisData;

            $report->update($data);

            return ApiResponse::success(
                new ReportResource($report->load(['equipment.components', 'condition', 'mechanismFail', 'componentFail', 'analist'])),
                'Se actualizó el reporte correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el reporte', 500, ['detail' => [$e->getMessage()]]);
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

            return ApiResponse::success(null, 'El reporte se eliminó correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el reporte', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function downloadPdf($id)
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        // Aumentar límites PCRE para HTML grande (imágenes base64, reportes extensos)
        ini_set('pcre.backtrack_limit', '10000000');
        ini_set('pcre.recursion_limit', '10000000');

        try {
            $report = Report::with([
                'equipment.components.componentType',
                'equipment.components.brand',
                'equipment.components.fields',
                'equipment.components.fieldValues',
                'equipment.company',
                'equipment.equipmentType',
                'equipment.ubication',
                'equipment.process',
                'condition',
                'priority',
                'mechanismFail',
                'componentFail',
                'analist'
            ])
            ->whereNull('deleted_at')
            ->findOrFail($id);

            $diagnosis = [];
            if ($report->diagnosis) {
                $decoded = self::decodeJsonField($report->diagnosis);
                if (is_array($decoded)) {
                    $componentsList = $report->equipment ? $report->equipment->components : collect();
                    $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray) {
                        foreach ($decoded as $index => $data) {
                            if ($componentsList->has($index)) {
                                $componentId = (string) $componentsList->get($index)->id;
                                if (is_array($data) && isset($data['text'])) {
                                    $diagnosis[$componentId] = $data['text'];
                                } elseif (is_string($data)) {
                                    $diagnosis[$componentId] = $data;
                                }
                            }
                        }
                    } else {
                        foreach ($decoded as $componentId => $data) {
                            $componentId = (string) $componentId;
                            if (is_array($data) && isset($data['text'])) {
                                $diagnosis[$componentId] = (string) ($data['text'] ?? '');
                            } elseif (is_string($data)) {
                                $diagnosis[$componentId] = $data;
                            }
                        }
                    }
                }
            }

            // Procesar recomendaciones para el PDF (claves normalizadas a string)
            $recommendations = [];
            if ($report->recommendations) {
                $decoded = self::decodeJsonField($report->recommendations);
                if (is_array($decoded)) {
                    $componentsList = $report->equipment ? $report->equipment->components : collect();
                    $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray) {
                        foreach ($decoded as $index => $data) {
                            if ($componentsList->has($index)) {
                                $componentId = (string) $componentsList->get($index)->id;
                                $recommendations[$componentId] = is_array($data) ? array_values(array_filter($data, fn ($r) => trim((string) $r) !== '')) : [];
                            }
                        }
                    } else {
                        foreach ($decoded as $componentId => $data) {
                            $componentId = (string) $componentId;
                            $recommendations[$componentId] = is_array($data) ? array_values(array_filter($data, fn ($r) => trim((string) $r) !== '')) : [];
                        }
                    }
                }
            }

            $componentImages = [];
            if ($report->diagnosis) {
                $decoded = self::decodeJsonField($report->diagnosis);
                if (is_array($decoded)) {
                    $componentsList = $report->equipment ? $report->equipment->components : collect();
                    $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                    if ($isNumericArray) {
                        foreach ($decoded as $index => $data) {
                            if ($componentsList->has($index) && is_array($data) && !empty($data['images'])) {
                                $componentId = (string) $componentsList->get($index)->id;
                                $componentImages[$componentId] = array_values(array_filter(array_map(function ($path) {
                                    return self::imageToBase64ForPdf($path);
                                }, $data['images'])));
                            }
                        }
                    } else {
                        foreach ($decoded as $componentId => $data) {
                            if (is_array($data) && !empty($data['images'])) {
                                $componentId = (string) $componentId;
                                $componentImages[$componentId] = array_values(array_filter(array_map(function ($path) {
                                    return self::imageToBase64ForPdf($path);
                                }, $data['images'])));
                            }
                        }
                    }
                }
            }

            $components = $report->equipment->components ?? collect();

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

            $equipmentName = $report->equipment?->name ?? 'reporte-' . $report->id;
            $filename = 'Informe Vibraciones ' . preg_replace('/[\\\\\/:*?"<>|]/', '_', trim($equipmentName)) . '.pdf';

            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al generar el PDF', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function export(): JsonResponse
    {
        return ApiResponse::success(null, 'En desarrollo');
    }

    public function getEmailRecipients($id): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $report = Report::with('equipment.company')->findOrFail($id);
        $company = $report->equipment?->company;

        if (!$company) {
            return ApiResponse::success([]);
        }

        $users = $company->users()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($users);
    }

    public function sendEmail(Request $request, $id): JsonResponse
    {
        if (!auth()->user()->can('consult report')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $report = Report::with([
                'equipment.components.componentType',
                'equipment.components.brand',
                'equipment.components.fields',
                'equipment.components.fieldValues',
                'equipment.company.users',
                'equipment.equipmentType',
                'equipment.ubication',
                'equipment.process',
                'condition',
                'priority',
                'mechanismFail',
                'componentFail',
                'analist',
            ])
                ->whereNull('deleted_at')
                ->findOrFail($id);

            $company = $report->equipment?->company;

            $recipients = [];
            $userIds = $request->input('user_ids', []);
            $additionalEmails = $request->input('additional_emails', []);

            if (is_array($userIds) && !empty($userIds) && $company) {
                $emails = $company->users()
                    ->whereIn('id', $userIds)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->pluck('email')
                    ->toArray();
                $recipients = array_merge($recipients, $emails);
            }

            if (is_array($additionalEmails)) {
                foreach ($additionalEmails as $e) {
                    $e = is_string($e) ? trim($e) : '';
                    if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = $e;
                    }
                }
            } elseif (is_string($additionalEmails) && $additionalEmails !== '') {
                $parts = array_map('trim', preg_split('/[\s,;]+/', $additionalEmails));
                foreach ($parts as $e) {
                    if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = $e;
                    }
                }
            }

            $recipients = array_unique(array_filter($recipients));

            if (empty($recipients)) {
                return ApiResponse::error('Seleccione al menos un destinatario o agregue un correo válido.', 422);
            }

            $pdf = $this->generatePdfForReport($report);
            $tempPath = storage_path('app/temp');
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $pdfPath = $tempPath . '/reporte-' . $report->id . '.pdf';
            $pdf->save($pdfPath);

            $equipmentName = $report->equipment?->name ?? 'N/A';
            $subject = 'Informe Vibraciones ' . $equipmentName;
            $pdfFilename = 'Informe Vibraciones ' . preg_replace('/[\\\\\/:*?"<>|]/', '_', trim($equipmentName)) . '.pdf';

            $firmaPath = public_path('images/firma_correo.jpeg');

            $analistName = $report->analist?->name ?? '—';
            $analistWorkstation = $report->analist?->workstation ?? '—';

            Mail::send([], [], function ($message) use ($pdfPath, $recipients, $subject, $equipmentName, $pdfFilename, $firmaPath, $analistName, $analistWorkstation) {
                $firmaCid = file_exists($firmaPath)
                    ? $message->embed($firmaPath)
                    : null;

                $htmlBody = '<p>Buen día Estimado Ingeniero, Cordial saludo,</p>
                    <p>Adjunto envío informe de análisis vibraciones ' . e($equipmentName) . '.</p>
                    <p>Posterior medición y análisis de vibraciones mecánicas, se realizan las recomendaciones más apropiadas en función de los hallazgos encontrados.</p>
                    <p>Cualquier duda e inquietud estaré atento.</p>
                    <p>Saludos!</p>
                    <p><strong>' . e($analistName) . '</strong><br>
                    ' . e($analistWorkstation) . '<br>
                    SYNERGY SMART S.A.S.<br>
                    Móvil: +57 (316) 4048830<br>
                    Fundación, Magdalena.</p>' .
                                        ($firmaCid ? '<p style="text-align: left;"><img src="' . $firmaCid . '" alt="Firma" style="width: 100%; max-width: 100%; height: 150px; object-fit: contain; object-position: left; display: block;"></p>' : '');

                $message->to($recipients)
                    ->subject($subject)
                    ->html($htmlBody);
                $message->attach($pdfPath, [
                    'as' => $pdfFilename,
                    'mime' => 'application/pdf',
                ]);
            });

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return ApiResponse::success(null, 'El correo se envió correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al enviar el correo', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Genera el PDF del reporte (lógica compartida con downloadPdf).
     */
    private function generatePdfForReport(Report $report)
    {
        // Aumentar límites PCRE para HTML grande (imágenes base64, reportes extensos)
        ini_set('pcre.backtrack_limit', '10000000');
        ini_set('pcre.recursion_limit', '10000000');

        $diagnosis = [];
        if ($report->diagnosis) {
            $decoded = self::decodeJsonField($report->diagnosis);
            if (is_array($decoded) && !empty($decoded)) {
                $componentsList = $report->equipment ? $report->equipment->components : collect();
                $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray) {
                    foreach ($decoded as $index => $data) {
                        if ($componentsList->has($index)) {
                            $componentId = (string) $componentsList->get($index)->id;
                            $diagnosis[$componentId] = is_array($data) && isset($data['text']) ? $data['text'] : (is_string($data) ? $data : '');
                        }
                    }
                } else {
                    foreach ($decoded as $componentId => $data) {
                        $componentId = (string) $componentId;
                        $diagnosis[$componentId] = is_array($data) && isset($data['text']) ? $data['text'] : (is_string($data) ? $data : '');
                    }
                }
            }
        }

        $recommendations = [];
        if ($report->recommendations) {
            $decoded = self::decodeJsonField($report->recommendations);
            if (is_array($decoded) && !empty($decoded)) {
                $componentsList = $report->equipment ? $report->equipment->components : collect();
                $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray) {
                    foreach ($decoded as $index => $data) {
                        if ($componentsList->has($index)) {
                            $componentId = (string) $componentsList->get($index)->id;
                            $recommendations[$componentId] = is_array($data) ? array_values(array_filter($data, fn ($r) => trim((string) $r) !== '')) : [];
                        }
                    }
                } else {
                    foreach ($decoded as $componentId => $data) {
                        $componentId = (string) $componentId;
                        $recommendations[$componentId] = is_array($data) ? array_values(array_filter($data, fn ($r) => trim((string) $r) !== '')) : [];
                    }
                }
            }
        }

        $componentImages = [];
        if ($report->diagnosis) {
            $decoded = self::decodeJsonField($report->diagnosis);
            if (is_array($decoded) && !empty($decoded)) {
                $componentsList = $report->equipment ? $report->equipment->components : collect();
                $isNumericArray = $componentsList->isNotEmpty() && array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray) {
                    foreach ($decoded as $index => $data) {
                        if ($componentsList->has($index) && is_array($data) && !empty($data['images'])) {
                            $componentId = (string) $componentsList->get($index)->id;
                            $componentImages[$componentId] = array_values(array_filter(array_map(
                                fn ($path) => self::imageToBase64ForPdf($path),
                                $data['images']
                            )));
                        }
                    }
                } else {
                    foreach ($decoded as $componentId => $data) {
                        if (is_array($data) && !empty($data['images'])) {
                            $componentId = (string) $componentId;
                            $componentImages[$componentId] = array_values(array_filter(array_map(
                                fn ($path) => self::imageToBase64ForPdf($path),
                                $data['images']
                            )));
                        }
                    }
                }
            }
        }

        $components = $report->equipment->components ?? [];
        $equipmentImage = ($report->equipment && !empty($report->equipment->image))
            ? self::imageToBase64ForPdf($report->equipment->image)
            : null;

        return PDF::loadView('reports.report-pdf', [
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
    }

    /**
     * Normaliza general_recommendations a array de strings no vacíos.
     */
    private static function normalizeGeneralRecommendations($value): array
    {
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_filter(array_map(function ($item) {
            return trim((string) $item);
        }, $value), fn ($s) => $s !== ''));
    }

    /**
     * Decodifica campo JSON que puede estar doble o múltiplemente codificado.
     * El modelo Report usa cast 'array', pero si se guardó con json_encode previo, queda doble codificado.
     */
    private static function decodeJsonField($value): array
    {
        if (empty($value)) {
            return [];
        }
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        while (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Convierte ruta de imagen en storage a data URI base64 para el PDF.
     * Acepta ruta relativa (ej. images/equipo.jpg), URL con /storage/ (ej. http://site.com/storage/images/x.jpg)
     * o ruta con barras invertidas; usa el disco 'public' de Laravel para resolver.
     */
    private static function imageToBase64ForPdf(string $path): string
    {
        $path = trim($path);
        if (empty($path)) {
            return '';
        }
        // Si ya es data URI, devolver tal cual
        if (str_starts_with($path, 'data:')) {
            return $path;
        }
        // Si es URL de nuestro storage (ej. http://site.com/storage/images/x.jpg), extraer ruta relativa
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($path);
            $pathPart = $parsed['path'] ?? '';
            if (preg_match('#/storage/(.+)$#', $pathPart, $m)) {
                $path = $m[1];
            } elseif (preg_match('#^/(.+)$#', $pathPart, $m)) {
                $path = $m[1];
            } else {
                return '';
            }
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        if (!Storage::disk('public')->exists($path)) {
            return '';
        }
        $fullPath = Storage::disk('public')->path($path);
        $mime = @mime_content_type($fullPath) ?: 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
    }
}

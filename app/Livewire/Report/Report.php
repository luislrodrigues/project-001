<?php

namespace App\Livewire\Report;

use App\Models\Equipment;
use App\Models\Report as ModelsReport;
use Livewire\Component;
use App\Models\Company;
use App\Models\Component as ModelsComponent;
use App\Models\ComponentFail;
use App\Models\ComponentType;
use App\Models\Condition;
use App\Models\MechanismFail;
use App\Models\User;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Carbon\Carbon;

class Report extends Component
{
    use WithFileUploads,WithPagination;

    public $equipment_search,$condition_search,$priority_search,$company_search,$reportIdToUpdate;
    public $equipment,$equipment_name,$location,$process,$diagnosis,$measurement_date,$image_1,$image_2,$condition,$failure_mechanism,$component_failure,$priority,$ot_created,$status_ot,$execution_date,$general_recommendations = [],$analist,$equipmentType,$condition_name,$failure_mechanism_name,$component_failure_name;

    public $reportData;
    public $recommendations = [];
    public $components = [];
    public $diagnostics = [];
    public $component_fails = [];
    public $mechanism_fails = [];
    public $conditions = [];


    protected $listeners = ['destroy','updatedConditions','updatedmechanismFails','updatedcomponentFails','item-saved'];


    public function mount()
    {
        $this->conditions = Condition::where('status','active')->whereNull('deleted_at')->get();
        $this->mechanism_fails = MechanismFail::where('status','active')->whereNull('deleted_at')->get();
        $this->component_fails = ComponentFail::where('status','active')->whereNull('deleted_at')->get();
    }

    public function render()
    {
       $companies = Company::whereNull('deleted_at')->where('status','active')->get();
       $users = User::where('status','active')->get();

        $reports = ModelsReport::query();
        if (!empty($this->equipment_search)) {
            $reports->where('equipment_id', $this->equipment_search);
         }
        if (!empty($this->condition_search)) {
            $reports->where('condition', $this->condition_search);
         }
        if (!empty($this->priority_search)) {
            $reports->where('priority', $this->priority_search);
         }
        if (!empty($this->company_search)) {
            $reports->whereHas('equipment', function ($query) {
                $query->where('company_id', $this->company_search);
            });
         }
        $equipments = Equipment::where('status','active')->get();

        $reports =$reports->paginate(10);
        return view('livewire.report.report', [
            'reports'      =>$reports,
            'equipments'   =>$equipments,
            'companies'    =>$companies,
            'users'        =>$users,
        ])->layout('layouts.app');
    }

    public function destroy($id)
    {
        if (auth()->user()->can('delete report')) {
            try{
                $report = ModelsReport::findOrFail($id);
                $report->status = 'inactive';
                $report->save();
                $report->delete();
                $this->dispatch('alertSuccess', 'El reporte se elimino correctamente.');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al aliminar el reporte: ' . $e->getMessage());
            }

        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }
    public function saveReport(){
        if (auth()->user()->can('save report')) {
            $rules = [
                'equipment'           => 'required|integer|exists:equipment,id',
                'measurement_date'    => 'required|date',
                'image_1'             => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_2'             => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'location'            => 'required|string|max:255',
                'process'             => 'required|string|max:255',
                'condition'           => 'required|integer|exists:conditions,id',
                'failure_mechanism'   => 'required|integer|exists:conditions,id',
                'component_failure'   => 'required|integer|exists:mechanism_fails,id',
                'priority'            => 'required',
                'analist'            =>'required|integer|exists:users,id',

            ];
            $this->validate($rules);
            try {
                $imagePath1 = null;
                $imagePath2 = null;

                if ($this->image_1)$imagePath1 = $this->image_1->store('images', 'public');
                if ($this->image_2)$imagePath2 = $this->image_2->store('images', 'public');

                $dataCreate = [
                    'equipment_id'                => $this->equipment,
                    'measurement_date'            => $this->measurement_date,
                    'condition_id'                   => $this->condition,
                    'priority'                    => $this->priority,
                    'mechanism_fail_id'           => $this->failure_mechanism,
                    'component_fail_id'           => $this->component_failure,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'general_recommendations' => is_array($this->general_recommendations) ? array_values(array_filter($this->general_recommendations, fn($s) => trim((string)$s) !== '')) : [],
                    'analist_id'                  => $this->analist,
                ];
                if (!empty($this->diagnostics)) {
                    $dataCreate['diagnosis'] = json_encode($this->diagnostics);
                }
                if (!empty($this->recommendations)) {
                    $dataCreate['recommendations'] = json_encode($this->recommendations);
                }
                if ($imagePath1)$dataCreate['image_1'] = $imagePath1;
                if ($imagePath2)$dataCreate['image_2'] = $imagePath2;
                ModelsReport::create($dataCreate);
                $this->resetForm();
                $this->dispatch('close-modal-report');
                $this->dispatch('alertSuccess','Se guardo el reporte correctamente');
            } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al guardar la empresa: ' . $e->getMessage());
            }

        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }
    public function editReport($reportId)
    {
        $this->loadReport($reportId);
        $this->dispatch('open-modal-edit-report');
    }
    public function showReportDetails($reportId)
    {
        $report = ModelsReport::findOrFail($reportId);
        $this->image_1 = $report->image_1;
        $this->image_2 = $report->image_2;
        $this->loadReport($reportId);
        $this->dispatch('open-modal-show-report');
    }

    public function loadReport($reportId){
        $report = ModelsReport::findOrFail($reportId);
        $recommendations = [];
        if(is_array($report->recommendations) && !empty($report->recommendations)){
            $recommendations = $report->recommendations;
        }
        $this->equipment = $report->equipment_id;
        $this->equipment_name = $report->equipment->name;
        $this->measurement_date = $report->measurement_date;
        $this->location = $report->equipment->ubication->name;
        $this->process = $report->equipment->process->name;
        $this->equipmentType = $report->equipment->equipmentType->name;
        $this->diagnostics = $report->diagnosis ? json_decode($report->diagnosis,true) : [];
        $this->condition = $report->condition->id;
        $this->condition_name = $report->condition->name;
        $this->priority = $report->priority;
        $this->failure_mechanism = $report->mechanismFail->id;
        $this->failure_mechanism_name = $report->mechanismFail->name;
        $this->component_failure = $report->componentFail->id;
        $this->component_failure_name = $report->componentFail->name;
        $this->recommendations = $recommendations ;
        $this->ot_created = $report->ot_created;
        $this->status_ot = $report->status_ot;
        $this->execution_date = $report->execution_date;
        $this->general_recommendations = $report->general_recommendations ?? [];
        $this->analist = $report->analist_id;
        $this->reportIdToUpdate = $reportId;
        $this->reportIdToUpdate = $reportId;
        $this->components = ModelsComponent::where('equipment_id',$report->equipment_id)->where('status','active')->get();
    }




    public function updateReport()
    {
        if (auth()->user()->can('edit report')) {
            $rules = [
                'equipment'           => 'required|integer|exists:equipment,id',
                'measurement_date'    => 'required|date',
                'image_2'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'condition'           => 'required|integer|exists:conditions,id',
                'failure_mechanism'   => 'required|integer|exists:conditions,id',
                'component_failure'   => 'required|integer|exists:mechanism_fails,id',
                'priority'            => 'required',
                'analist'             => 'required|integer|exists:users,id',
            ];
            $imagePath1 = null;
            $imagePath2 = null;
            if ($this->image_1) {
                $rules['image_1'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
                $imagePath1 = $this->image_1->store('images', 'public');
            }
            if ($this->image_2) {
                $rules['image_2'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
                $imagePath2 = $this->image_2->store('images', 'public');
            }
            $this->validate($rules);
            try{
                $report = ModelsReport::findOrFail($this->reportIdToUpdate);
                $dataToUpdate = [
                    'equipment_id'                => $this->equipment,
                    'measurement_date'            => $this->measurement_date,
                    'condition_id'                => $this->condition,
                    'priority'                    => $this->priority,
                    'mechanism_fail_id'           => $this->failure_mechanism,
                    'component_fail_id'           => $this->component_failure,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'general_recommendations' => is_array($this->general_recommendations) ? array_values(array_filter($this->general_recommendations, fn($s) => trim((string)$s) !== '')) : [],
                    'analist_id'                  => $this->analist,
                ];
                if (!empty($this->diagnostics)) {
                    $dataToUpdate['diagnosis'] = json_encode($this->diagnostics);
                }
                if (!empty($this->recommendations)) {
                    $dataToUpdate['recommendations'] = $this->recommendations;
                }
                if ($imagePath1)$dataToUpdate['image_1'] = $imagePath1;
                if ($imagePath2)$dataToUpdate['image_2'] = $imagePath2;
                $report->update($dataToUpdate);
                $this->resetForm();
                $this->dispatch('close-modal-report');
                $this->dispatch('alertSuccess', 'Se actualizó el reporte correctamente');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al actualizar el reporte: ' . $e->getMessage());
            }
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }
    public function resetForm(){
        $this->reset(['reportIdToUpdate','equipment','location','process','measurement_date','equipment_name','image_1','image_2','condition','failure_mechanism','component_failure','priority','ot_created','status_ot','execution_date','general_recommendations','analist','equipmentType','condition_name','failure_mechanism_name','component_failure_name']);
        $this->diagnostics = [];
        $this->components = [];
        $this->recommendations = [];
        $this->resetValidation();
    }

    public function export()
    {
        try {
            // Obtener los datos con eager loading para evitar N+1 queries
            $reports = ModelsReport::query()
                ->with([
                    'equipment.company',
                    'equipment.equipmentType',
                    'equipment.ubication',
                    'equipment.process',
                    'equipment.components.componentType',
                    'equipment.components.brand',
                    'equipment.components.fields.value',
                    'condition',
                    'mechanismFail',
                    'componentFail',
                    'analist'
                ]);

            // Aplicar filtros
            if (!empty($this->equipment_search)) {
                $reports->where('equipment_id', $this->equipment_search);
            }
            if (!empty($this->condition_search)) {
                $reports->where('condition_id', $this->condition_search);
            }
            if (!empty($this->priority_search)) {
                $reports->where('priority', $this->priority_search);
            }
            if (!empty($this->company_search)) {
                $reports->whereHas('equipment', function ($query) {
                    $query->where('company_id', $this->company_search);
                });
            }

            $reports = $reports->get();

            if(count($reports) === 0){
                $this->dispatch('alertFailed', 'No se encontraron reportes con los filtros seleccionados.');
                return;
            }

            // Crear un nuevo objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('DIAGNÓSTICOS');

            // Encabezado del documento
            $sheet->setCellValue('A1', 'REPORTE DE DIAGNÓSTICOS');
            $sheet->mergeCells('A1:AF1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1e40af'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Información adicional
            $sheet->setCellValue('A2', 'Fecha de Exportación: ' . now()->format('d/m/Y H:i:s'));
            $sheet->setCellValue('A3', 'Total de Reportes: ' . $reports->count());
            $sheet->mergeCells('A2:AF2');
            $sheet->mergeCells('A3:AF3');
            $sheet->getStyle('A2:A3')->applyFromArray([
                'font' => ['size' => 10, 'italic' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Encabezados de columnas (empiezan en la fila 4)
            $headings = [
                'EMPRESA', 'EQUIPO', 'TIPO EQUIPO', 'FECHA MEDICIÓN', 'UBICACIÓN', 'PROCESO',
                'TIPO COMPONENTE', 'TAG', 'DESCRIPCIÓN', 'CODIGO', 'POT (HP)', 'RPM', 'MARCA',
                'REFERENCIA MOTOR', 'FRAME', 'RODA. LL', 'ROD. LA', 'REF. CHUMACERAS', 'ROD. CHUMACERAS',
                'DIAGNÓSTICO', 'CONDICIÓN', 'MECANISMO FALLA', 'COMPONENTE EN FALLA', 'RECOMENDACIÓN',
                'PRIORIDAD', 'OT CREADA', 'ESTADO DE OT', 'FECHA EJECUCIÓN', 'VERIFICACIÓN RECOMENDACIÓN',
                'ANALISTA', 'Mes', 'Año'
            ];
            $sheet->fromArray($headings, NULL, 'A4');

            // Estilos para encabezados
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF3b82f6'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A4:AF4')->applyFromArray($headerStyle);
            $sheet->getRowDimension(4)->setRowHeight(40);

            // Estilo para datos
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ];

            // Llenar datos
            $row = 5;
            foreach ($reports as $report) {
                $diagnosis = is_string($report->diagnosis) ? json_decode($report->diagnosis, true) : ($report->diagnosis ?? []);
                $recommendations = is_array($report->recommendations) ? $report->recommendations : (is_string($report->recommendations) ? json_decode($report->recommendations, true) : []);

                if($report->equipment && $report->equipment->components) {
                    foreach ($report->equipment->components as $component) {
                        // Inicializar campos del componente
                        $potencia = '';
                        $velocidad = '';
                        $marca = $component->brand->name ?? '';
                        $referencia = '';
                        $frame = '';
                        $rodamientoLL = '';
                        $rodamientoLA = '';
                        $refChumacera = '';
                        $rodChumaceras = '';

                        // Extraer campos del componente
                        if($component->fields) {
                            foreach($component->fields as $field) {
                                $fieldName = $field->name ?? '';
                                $fieldValue = $field->value->value ?? null;

                                switch($fieldName) {
                                    case 'Potencia':
                                        $potencia = $fieldValue ?? '';
                                        break;
                                    case 'Velocidad':
                                        $velocidad = $fieldValue ?? '';
                                        break;
                                    case 'Referencia':
                                        $referencia = $fieldValue ?? '';
                                        break;
                                    case 'Frame':
                                        $frame = $fieldValue ?? '';
                                        break;
                                    case 'Rodamiento lado libre':
                                        $rodamientoLL = $fieldValue ?? '';
                                        break;
                                    case 'Rodamiento lado acople':
                                        $rodamientoLA = $fieldValue ?? '';
                                        break;
                                    case 'Referencia Chumacera':
                                        $refChumacera = $fieldValue ?? '';
                                        break;
                                    case 'Referencia Rodamientos':
                                        $rodChumaceras = $fieldValue ?? '';
                                        break;
                                }
                            }
                        }

                        // Diagnóstico y recomendaciones
                        $componentDiagnosis = (is_array($diagnosis) && array_key_exists($component->id, $diagnosis))
                            ? $diagnosis[$component->id]
                            : '';

                        $recommendationsString = '';
                        if (is_array($recommendations) && array_key_exists($component->id, $recommendations)) {
                            if (is_array($recommendations[$component->id])) {
                                $recommendationsString = implode(' ; ', array_filter($recommendations[$component->id]));
                            } else {
                                $recommendationsString = $recommendations[$component->id];
                            }
                        }

                        // Fechas formateadas
                        $measurementDate = null;
                        if ($report->measurement_date) {
                            try {
                                $measurementDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Carbon::parse($report->measurement_date));
                            } catch (\Exception $e) {
                                $measurementDate = null;
                            }
                        }

                        $executionDate = null;
                        if ($report->execution_date) {
                            try {
                                $executionDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Carbon::parse($report->execution_date));
                            } catch (\Exception $e) {
                                $executionDate = null;
                            }
                        }

                        $month = '';
                        $year = '';
                        if ($report->execution_date) {
                            try {
                                $execDate = Carbon::parse($report->execution_date);
                                $month = $execDate->format('m');
                                $year = $execDate->format('Y');
                            } catch (\Exception $e) {
                                $month = '';
                                $year = '';
                            }
                        }

                        // Llenar datos
                        $sheet->setCellValue('A' . $row, $report->equipment->company->name ?? '');
                        $sheet->setCellValue('B' . $row, $report->equipment->name ?? '');
                        $sheet->setCellValue('C' . $row, $report->equipment->equipmentType->name ?? '');

                        if ($measurementDate) {
                            $sheet->setCellValue('D' . $row, $measurementDate);
                            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                        }

                        $sheet->setCellValue('E' . $row, $report->equipment->ubication->name ?? '');
                        $sheet->setCellValue('F' . $row, $report->equipment->process->name ?? '');
                        $sheet->setCellValue('G' . $row, $component->componentType->name ?? '');
                        $sheet->setCellValue('H' . $row, $report->equipment->tag ?? '');
                        $sheet->setCellValue('I' . $row, $report->equipment->description ?? '');
                        $sheet->setCellValue('J' . $row, $report->equipment->code ?? '');
                        $sheet->setCellValue('K' . $row, $potencia);
                        $sheet->setCellValue('L' . $row, $velocidad);
                        $sheet->setCellValue('M' . $row, $marca);
                        $sheet->setCellValue('N' . $row, $referencia);
                        $sheet->setCellValue('O' . $row, $frame);
                        $sheet->setCellValue('P' . $row, $rodamientoLL);
                        $sheet->setCellValue('Q' . $row, $rodamientoLA);
                        $sheet->setCellValue('R' . $row, $refChumacera);
                        $sheet->setCellValue('S' . $row, $rodChumaceras);
                        $sheet->setCellValue('T' . $row, $componentDiagnosis);
                        $sheet->setCellValue('U' . $row, $report->condition->name ?? '');
                        $sheet->setCellValue('V' . $row, $report->mechanismFail->name ?? '');
                        $sheet->setCellValue('W' . $row, $report->componentFail->name ?? '');
                        $sheet->setCellValue('X' . $row, $recommendationsString);
                        $sheet->setCellValue('Y' . $row, $report->priority ?? '');
                        $sheet->setCellValue('Z' . $row, $report->ot_created ?? '');
                        $sheet->setCellValue('AA' . $row, $report->status_ot ?? '');

                        if ($executionDate) {
                            $sheet->setCellValue('AB' . $row, $executionDate);
                            $sheet->getStyle('AB' . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                        }

                        $sheet->setCellValue('AC' . $row, is_array($report->general_recommendations) ? implode("\n", $report->general_recommendations) : '');
                        $sheet->setCellValue('AD' . $row, $report->analist->name ?? '');
                        $sheet->setCellValue('AE' . $row, $month ?: '');
                        $sheet->setCellValue('AF' . $row, $year ?: '');

                        // Aplicar estilo a la fila
                        $sheet->getStyle('A' . $row . ':AF' . $row)->applyFromArray($dataStyle);

                        // Alternar colores de filas para mejor legibilidad
                        if ($row % 2 == 0) {
                            $sheet->getStyle('A' . $row . ':AF' . $row)->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF9FAFB');
                        }

                        $row++;
                    }
                }
            }

            // Ajustar ancho de columnas
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                       'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF'];

            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);

                // Limitar ancho máximo para columnas largas
                if (in_array($column, ['X', 'T', 'I', 'AC'])) {
                    $sheet->getColumnDimension($column)->setWidth(30);
                }
            }

            // Congelar primera fila de datos (fila 4)
            $sheet->freezePane('A5');

            // Agregar filtros automáticos
            $sheet->setAutoFilter('A4:AF' . ($row - 1));

            // Proteger hoja (opcional, puede comentarse si no se necesita)
            // $sheet->getProtection()->setSheet(true);

            // Generar nombre de archivo con fecha
            $fileName = 'diagnosticos_' . now()->format('Y-m-d_His') . '.xlsx';

            // Guardar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $temp_file = tempnam(sys_get_temp_dir(), 'report_');
            $writer->save($temp_file);

            $this->dispatch('alertSuccess', 'Excel generado correctamente');

            return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al generar el Excel: ' . $e->getMessage());
            \Log::error('Error al exportar Excel: ' . $e->getMessage());
            return null;
        }
    }

    public function generatePdf($id)
    {
        $this->reportData = ModelsReport::with([
            'equipment.components.fields.value',
            'equipment.components.componentType',
            'equipment.components.brand',
            'equipment.company',
            'equipment.equipmentType',
            'equipment.ubication',
            'equipment.process',
            'condition',
            'mechanismFail',
            'componentFail',
        ])->findOrFail($id);
        $components = $this->reportData->equipment->components ?? collect();

        $diagnosis = [];
        if ($this->reportData->diagnosis) {
            $decoded = is_string($this->reportData->diagnosis) ? json_decode($this->reportData->diagnosis, true) : $this->reportData->diagnosis;
            if (is_array($decoded)) {
                $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray && $components->isNotEmpty()) {
                    foreach ($decoded as $index => $data) {
                        if ($components->has($index)) {
                            $componentId = $components->get($index)->id;
                            $diagnosis[$componentId] = is_array($data) && isset($data['text']) ? $data['text'] : (is_string($data) ? $data : '');
                        }
                    }
                } else {
                    foreach ($decoded as $componentId => $data) {
                        $diagnosis[$componentId] = is_array($data) && isset($data['text']) ? $data['text'] : (is_string($data) ? $data : '');
                    }
                }
            }
        }

        $recommendations = [];
        if ($this->reportData->recommendations) {
            $decoded = is_string($this->reportData->recommendations) ? json_decode($this->reportData->recommendations, true) : $this->reportData->recommendations;
            if (is_array($decoded)) {
                $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray && $components->isNotEmpty()) {
                    foreach ($decoded as $index => $data) {
                        if ($components->has($index)) {
                            $componentId = $components->get($index)->id;
                            $recommendations[$componentId] = is_array($data) ? $data : [];
                        }
                    }
                } else {
                    $recommendations = $decoded;
                }
            }
        }

        $componentImages = [];
        if ($this->reportData->diagnosis) {
            $decoded = is_string($this->reportData->diagnosis) ? json_decode($this->reportData->diagnosis, true) : $this->reportData->diagnosis;
            if (is_array($decoded)) {
                $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
                if ($isNumericArray && $components->isNotEmpty()) {
                    foreach ($decoded as $index => $data) {
                        if ($components->has($index) && is_array($data) && isset($data['images'])) {
                            $componentId = $components->get($index)->id;
                            $componentImages[$componentId] = array_map([$this, 'imageToBase64ForPdf'], $data['images']);
                        }
                    }
                } else {
                    foreach ($decoded as $componentId => $data) {
                        if (is_array($data) && isset($data['images'])) {
                            $componentImages[$componentId] = array_map([$this, 'imageToBase64ForPdf'], $data['images']);
                        }
                    }
                }
            }
        }

        $equipmentImage = null;
        if ($this->reportData->equipment && !empty($this->reportData->equipment->image)) {
            $equipmentImage = $this->imageToBase64ForPdf($this->reportData->equipment->image);
        }

        $pdf = PDF::loadView('livewire.report.report-pdf', [
            'reportData' => $this->reportData,
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
        return $pdf;
    }

    /**
     * Convierte una imagen (ruta relativa en storage) a data URI base64 para que mPDF la muestre siempre en el PDF.
     */
    private function imageToBase64ForPdf(string $path): string
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

    public function downloadPdf($id)
    {
        try{
            $pdf = $this->generatePdf($id);
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, 'reporte.pdf');

        }catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al descargar el pdf' . $e->getMessage());
        }
    }

    public function sendEmailReport(){
        try {
            $equipment = Equipment::with('company')->findOrFail($this->equipment);
            $recipients = json_decode($equipment->company->emails);

            if(is_array($recipients) && !empty($recipients)){
                $pdf = $this->generatePdf($this->reportIdToUpdate);

                $tempPath = storage_path('app/temp');

                if (!file_exists($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }

                $pdfPath = $tempPath . '/reporte.pdf';
                $pdf->save($pdfPath);

                $tz = config('app.timezone', 'America/Bogota');
                $subject = 'Envio de reporte - ' . now($tz)->format('Y-m-d H:i:s');

                Mail::send([], [], function ($message) use ($pdfPath, $recipients, $subject) {
                    $message->to($recipients)
                            ->subject($subject)
                            ->html('Este es un correo de prueba.');

                    $message->attach($pdfPath);
                });
                $this->dispatch('alertSuccess', 'Se envio correctamente');

            }else{
                $this->dispatch('alertInfo', 'No hay correos en la lista de destinatarios.');
            }

        }catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al enviar la notificacion: ' . $e->getMessage());
        }
    }

    public function addRecommendation($componentId)
    {
        $this->recommendations[$componentId][] = ''; // Agregar una nueva recomendación al componente específico
    }

    public function deleteRecommendation($componentId, $index)
    {
    unset($this->recommendations[$componentId][$index]);
    $this->recommendations[$componentId] = array_values($this->recommendations[$componentId]); // Reindexar el array
    }

    public function updatedEquipment($value)
    {
        if($value){
            $equipment = Equipment::findOrFail($value);
            if($equipment){
                $this->location = $equipment->ubication->name;
                $this->process = $equipment->process->name;
                $this->equipmentType = $equipment->equipmentType->name;
                $this->components = ModelsComponent::where('equipment_id',$value)->where('status','active')->get();
                if($this->components){
                    foreach ($this->components as $component) {
                        $this->recommendations[$component->id] = [];
                    }
                }else{
                    $this->recommendations = [];
                    $this->diagnostics = [];
                    $this->components = [];
                }
            }else{
                $this->recommendations = [];
                $this->diagnostics = [];
                $this->components = [];
                $this->reset(['location','process','equipmentType']);
            }
        }else{
            $this->recommendations = [];
            $this->diagnostics = [];
            $this->components = [];
            $this->reset(['location','process','equipmentType']);
        }
    }

    public function openManageModal($field)
    {
        $this->dispatch('open-modal-select', $field);
    }

    // Escuchar cuando se guarda un item para seleccionarlo automáticamente
    public function itemSaved($data)
    {
        $field = $data['field'] ?? null;
        $id = $data['id'] ?? null;

        if ($field && $id) {
            // Actualizar la propiedad correspondiente con el ID del nuevo registro
            switch ($field) {
                case 'condition':
                    $this->condition = $id;
                    break;
                case 'failure_mechanism':
                    $this->failure_mechanism = $id;
                    break;
                case 'component_failure':
                    $this->component_failure = $id;
                    break;
            }
        }
    }

    public function updatedConditions()
    {
        $this->condition = '';
        $this->conditions = Condition::where('status', 'active')->whereNull('deleted_at')->get();
        $this->dispatch('updateOptions', $this->conditions);
    }
    public function updatedmechanismFails()
    {
        $this->failure_mechanism = '';
        $this->mechanism_fails = mechanismFail::where('status', 'active')->whereNull('deleted_at')->get();
        $this->dispatch('updateOptions', $this->mechanism_fails);
    }
    public function updatedcomponentFails()
    {
        $this->component_failure = '';
        $this->component_fails = componentFail::where('status', 'active')->whereNull('deleted_at')->get();
        $this->dispatch('updateOptions', $this->component_fails);
    }
}

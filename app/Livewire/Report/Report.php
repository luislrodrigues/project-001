<?php

namespace App\Livewire\Report;

use App\Models\Equipment;
use App\Models\Report as ModelsReport;
use Livewire\Component;
use App\Models\Company;
use App\Models\Component as ModelsComponent;
use App\Models\ComponentType;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
class Report extends Component
{
    use WithFileUploads;

    public $equipment_search,$condition_search,$priority_search,$company_search,$reportIdToUpdate;
    public $equipment,$location,$process,$diagnosis,$measurement_date,$image_1,$image_2,$condition,$failure_mechanism,$component_failure,$priority,$ot_created,$status_ot,$execution_date,$verification_recommendation,$analist;

    public $reportData;
    public $recommendations = [];
    public $components = [];
    public $diagnostics = [];


    public function render()
    {
       $companies = Company::whereNull('deleted_at')->where('status','active')->get();

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
            'reports'   =>$reports,
            'equipments'   =>$equipments,
            'companies'        =>$companies,
        ])->layout('layouts.app');
    }

    public function updatedEquipment($value)
    {
        $this->components = ModelsComponent::where('equipment_id',$value)->where('status','active')->get();
    }

    protected $listeners = ['destroy'];
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
                'condition'           => 'required',
                'failure_mechanism'   => 'required',
                'component_failure'   => 'required',
                'priority'            => 'required',
                'analist'             => 'required',
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
                    'location'                    => $this->location,
                    'process'                     => $this->process,
                    'condition'                   => $this->condition,
                    'priority'                    => $this->priority,
                    'failure_mechanism'           => $this->failure_mechanism,
                    'component_failure'           => $this->component_failure,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'verification_recommendation' => $this->verification_recommendation,
                    'analist'                     => $this->analist,
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
        $this->equipment = $report->equipment_id;
        $this->measurement_date = $report->measurement_date;
        $this->location = $report->location;
        $this->process = $report->process;
        $this->diagnostics = $report->diagnosis ? json_decode($report->diagnosis,true) : [];
        $this->condition = $report->condition;
        $this->priority = $report->priority;
        $this->failure_mechanism = $report->failure_mechanism;
        $this->component_failure = $report->component_failure;
        $this->recommendations = is_array($report->recommendations) ? $report->recommendations : [] ;
        $this->ot_created = $report->ot_created;
        $this->status_ot = $report->status_ot;
        $this->execution_date = $report->execution_date;
        $this->verification_recommendation = $report->verification_recommendation;
        $this->analist = $report->analist;
        $this->reportIdToUpdate = $reportId;
        $this->reportIdToUpdate = $reportId;
        $this->updatedEquipment( $report->equipment_id);
    }

    
    

    public function updateReport()
    {
        if (auth()->user()->can('edit report')) {
            $rules = [
                'equipment'           => 'required|integer|exists:equipment,id',
                'measurement_date'    => 'required|date',
                'image_2'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'location'            => 'required|string|max:255',
                'process'             => 'required|string|max:255',
                'condition'           => 'required',
                'failure_mechanism'   => 'required',
                'component_failure'   => 'required',
                'priority'            => 'required',
                'analist'             => 'required',
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
                    'location'                    => $this->location,
                    'process'                     => $this->process,
                    'condition'                   => $this->condition,
                    'priority'                    => $this->priority,
                    'failure_mechanism'           => $this->failure_mechanism,
                    'component_failure'           => $this->component_failure,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'verification_recommendation' => $this->verification_recommendation,
                    'analist'                     => $this->analist,
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
        $this->reset(['reportIdToUpdate','equipment','location','process','measurement_date','image_1','image_2','condition','failure_mechanism','component_failure','priority','ot_created','status_ot','execution_date','verification_recommendation','analist']);
        $this->diagnostics = [];
        $this->recommendations = [];
        $this->resetValidation();
    }

    public function export()
    {
        // Obtener los datos del request
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
        $reports = $reports->get();

        if(count($reports) > 0){
            // Crear un nuevo objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Cambiar el nombre de la hoja
            $sheet->setTitle('DIAGNÓSTICOS');

            // Añadir encabezados
            $headings = [
                'EMPRESA', 'FECHA MEDICIÓN', 'UBICACIÓN', 'PROCESO', 'TIPO EQUIPO', 'TAG', 'DESCRIPCIÓN', 'EQUIPO', 'POT (HP)', 
                'RPM', 'MARCA', 'REFERENCIA MOTOR', 'FRAME', 'RODA. LL', 'ROD. LA', 'REF. CHUMACERAS', 'ROD. CHUMACERAS', 
                'DIAGNÓSTICO', 'CONDICIÓN', 'MECANISMO FALLA', 'COMPONENTE EN FALLA', 'RECOMENDACIÓN', 'PRIORIDAD', 'OT CREADA', 
                'ESTADO DE OT', 'FECHA EJECUCIÓN', 'VERIFICACIÓN RECOMENDACIÓN', 'ANALISTA', 'Mes', 'Año'
            ];
            $sheet->fromArray($headings, NULL, 'A1');

            // Establecer colores para las cabeceras
            $greenStyle = [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => 'FF000000', // Letra negra
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'A9D08E',
                    ],
                ],
            ];

            $blueStyle = [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => 'FFFFFFFF', // Letra blanca
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '0070C0',
                    ],
                ],
            ];
        
            // Aplicar estilo verde hasta la columna AB
            $sheet->getStyle('A1:AB1')->applyFromArray($greenStyle);
        
            // Aplicar estilo azul a las columnas AC y AD
            $sheet->getStyle('AC1:AD1')->applyFromArray($blueStyle);

            // Añadir datos al sheet
            $row = 2;
            foreach ($reports as $report) {
                $sheet->setCellValue('A' . $row, $report->equipment->company->name);
                $sheet->setCellValue('B' . $row, $report->measurement_date);
                $sheet->setCellValue('C' . $row, $report->location);
                $sheet->setCellValue('D' . $row, $report->process);
                $sheet->setCellValue('E' . $row, $report->equipment->component->equipmentType->name);
                $sheet->setCellValue('F' . $row, $report->equipment->tag);
                $sheet->setCellValue('G' . $row, $report->equipment->description);
                $sheet->setCellValue('H' . $row, $report->equipment->name);


                // foreach($report->equipment->fields as $field){
                //     if($field['name'] == 'Potencia' && $field->value)$sheet->setCellValue('I' . $row, $field->value->value);
                //     if($field['name'] == 'Velocidad' && $field->value)$sheet->setCellValue('J' . $row, $field->value->value);
                //     if($field['name'] == 'Referencia' && $field->value)$sheet->setCellValue('L' . $row, $field->value->value);
                //     if($field['name'] == 'Frame' && $field->value)$sheet->setCellValue('M' . $row, $field->value->value);
                //     if($field['name'] == 'Rodamiento lado libre' && $field->value)$sheet->setCellValue('N' . $row, $field->value->value);
                //     if($field['name'] == 'Rodamiento lado acople' && $field->value)$sheet->setCellValue('O' . $row, $field->value->value);
                //     if($field['name'] == 'Referencia Chumacera' && $field->value)$sheet->setCellValue('P' . $row, $field->value->value);
                //     if($field['name'] == 'Referencia Rodamientos' && $field->value)$sheet->setCellValue('Q' . $row, $field->value->value);
                // }
                $recommendationsString = implode(' ; ', $report->recommendations);
                // $sheet->setCellValue('K' . $row, $report->equipment->brand->name);
                $sheet->setCellValue('R' . $row, $report->ot_created);
                $sheet->setCellValue('R' . $row, $report->diagnosis);
                $sheet->setCellValue('S' . $row, $report->condition);
                $sheet->setCellValue('T' . $row, $report->failure_mechanism);
                $sheet->setCellValue('U' . $row, $report->component_failure);
                $sheet->setCellValue('V' . $row, $recommendationsString);
                $sheet->setCellValue('W' . $row, $report->priority);
                $sheet->setCellValue('X' . $row, $report->ot_created);
                $sheet->setCellValue('Y' . $row, $report->status_ot);
                $sheet->setCellValue('Z' . $row, $report->execution_date);
                $sheet->setCellValue('AA' . $row, $report->verification_recommendation);
                $sheet->setCellValue('AB' . $row, $report->analist);
                $sheet->setCellValue('AC' . $row, date('m', strtotime($report->execution_date)));
                $sheet->setCellValue('AD' . $row, date('Y', strtotime($report->execution_date)));
                $row++;
            }

            // Ajustar el ancho de las columnas al contenido
            foreach (range('A', 'AD') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            // Guardar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reports.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
        }else{
            $this->dispatch('alertFailed', 'No se encontraron reportes.');
        }



    }

    public function addRecommendation()
    {
        $this->recommendations[] = ""; // Agrega un nuevo campo vacío
    }
    public function deleteRecommendation($index)
    {
        if (isset($this->recommendations[$index])) {
            unset($this->recommendations[$index]);
            $this->recommendations = array_values($this->recommendations); // Reindexar el array
        }
    }

    public function generatePdf($id)
    {
        $this->reportData = ModelsReport::findOrFail($id);
        $diagnosis = json_decode($this->reportData->diagnosis, true);
        if (is_array($diagnosis)) {
            $diagnosisWithNames = [];
            foreach ($diagnosis as $index => $value) {
                $componentType = ComponentType::findOrFail($index);
                $diagnosisWithNames[$componentType->name] = $value;
            }
            $this->reportData->diagnosis = $diagnosisWithNames;
        }
        $pdf = Pdf::loadView('livewire.report.report-pdf', ['reportData' => $this->reportData]);
        return $pdf;
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
            $pdf = $this->generatePdf($this->reportIdToUpdate);
    
            $tempPath = storage_path('app/temp');
    
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
    
            $pdfPath = $tempPath . '/reporte.pdf';
            $pdf->save($pdfPath);
    
            Mail::send([], [], function ($message) use ($pdfPath) {
                $message->to('luisleonardor4@gmail.com')
                        ->subject('Envio de reporte')
                        ->html('Este es un correo de prueba.');
    
                $message->attach($pdfPath);
            });
            $this->dispatch('alertSuccess', 'Se envio correctamente');
        }catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al enviar la notificacion: ' . $e->getMessage());
        }
    }
}

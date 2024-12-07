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
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use Livewire\WithPagination;

class Report extends Component
{
    use WithFileUploads,WithPagination;

    public $equipment_search,$condition_search,$priority_search,$company_search,$reportIdToUpdate;
    public $equipment,$equipment_name,$location,$process,$diagnosis,$measurement_date,$image_1,$image_2,$condition,$failure_mechanism,$component_failure,$priority,$ot_created,$status_ot,$execution_date,$verification_recommendation,$analist,$equipmentType,$condition_name,$failure_mechanism_name,$component_failure_name;

    public $reportData;
    public $recommendations = [];
    public $components = [];
    public $diagnostics = [];
    public $component_fails = [];
    public $mechanism_fails = [];
    public $conditions = [];
    
    
    protected $listeners = ['destroy','updatedConditions','updatedmechanismFails','updatedcomponentFails'];


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
                    'verification_recommendation' => $this->verification_recommendation,
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
        $this->verification_recommendation = $report->verification_recommendation;
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
                    'verification_recommendation' => $this->verification_recommendation,
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
        $this->reset(['reportIdToUpdate','equipment','location','process','measurement_date','equipment_name','image_1','image_2','condition','failure_mechanism','component_failure','priority','ot_created','status_ot','execution_date','verification_recommendation','analist','equipmentType','condition_name','failure_mechanism_name','component_failure_name']);
        $this->diagnostics = [];
        $this->components = [];
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
                'EMPRESA', 'EQUIPO', 'TIPO EQUIPO','FECHA MEDICIÓN', 'UBICACIÓN', 'PROCESO', 'TIPO COMPONENTE', 'TAG', 'DESCRIPCIÓN','CODIGO', 'POT (HP)', 
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
            $sheet->getStyle('A1:AC1')->applyFromArray($greenStyle);
        
            // Aplicar estilo azul a las columnas AC y AD
            $sheet->getStyle('AD1:AE1')->applyFromArray($blueStyle);

            // Añadir datos al sheet
            $row = 2;
            foreach ($reports as $report) {

     
                foreach ($report->equipment->components as $component) {
                    $sheet->setCellValue('A' . $row, $report->equipment->company->name);
                    $sheet->setCellValue('B' . $row, $report->equipment->name);
                    $sheet->setCellValue('C' . $row, $report->equipment->equipmentType->name);

                    $sheet->setCellValue('D' . $row, $report->measurement_date);
                    $sheet->setCellValue('E' . $row, $report->equipment->ubication->name);
                    $sheet->setCellValue('F' . $row, $report->equipment->process->name);
                    $sheet->setCellValue('G' . $row, $component->componentType->name);
    
                    $sheet->setCellValue('H' . $row, $report->equipment->tag);
                    $sheet->setCellValue('I' . $row, $report->equipment->description);
                    $sheet->setCellValue('J' . $row, $report->equipment->code);

                    foreach($component->fields as $field){
                        if($field['name'] == 'Potencia' && $field->value)$sheet->setCellValue('K' . $row, $field->value->value);
                        if($field['name'] == 'Velocidad' && $field->value)$sheet->setCellValue('L' . $row, $field->value->value);
                         $sheet->setCellValue('M' . $row, $component->brand->name);
                         if($field['name'] == 'Referencia' && $field->value)$sheet->setCellValue('N' . $row, $field->value->value);


                        if($field['name'] == 'Frame' && $field->value)$sheet->setCellValue('O' . $row, $field->value->value);
                        if($field['name'] == 'Rodamiento lado libre' && $field->value)$sheet->setCellValue('P' . $row, $field->value->value);
                        if($field['name'] == 'Rodamiento lado acople' && $field->value)$sheet->setCellValue('Q' . $row, $field->value->value);
                        if($field['name'] == 'Referencia Chumacera' && $field->value)$sheet->setCellValue('R' . $row, $field->value->value);
                        if($field['name'] == 'Referencia Rodamientos' && $field->value)$sheet->setCellValue('S' . $row, $field->value->value);
                    }
                    $recommendations = ($report->recommendations);
                    $recommendationsString = (is_array($recommendations) && array_key_exists($component->id, $recommendations) && is_array($recommendations[$component->id])) 
                        ? implode(' ; ', $recommendations[$component->id]) 
                        : '';
                    $diagnosis = json_decode($report->diagnosis,true);
                    $componentDiagnosis = (is_array($diagnosis) && array_key_exists($component->id, $diagnosis)) ? $diagnosis[$component->id] : '';
                    $sheet->setCellValue('T' . $row,$componentDiagnosis );
                    $sheet->setCellValue('U' . $row, $report->condition->name);
                    $sheet->setCellValue('V' . $row, $report->mechanismFail->name);
                    $sheet->setCellValue('W' . $row, $report->componentFail->name);
                    $sheet->setCellValue('X' . $row, $recommendationsString);
                    $sheet->setCellValue('Y' . $row, $report->priority);
                    $sheet->setCellValue('Z' . $row, $report->ot_created);
                    $sheet->setCellValue('AA' . $row, $report->status_ot);
                    $sheet->setCellValue('AB' . $row, $report->execution_date);
                    $sheet->setCellValue('AC' . $row, $report->verification_recommendation);
                    $sheet->setCellValue('AD' . $row, $report->analist->name);
                    $sheet->setCellValue('AE' . $row, date('m', strtotime($report->execution_date)));
                    $sheet->setCellValue('AF' . $row, date('Y', strtotime($report->execution_date)));
                    $row++;
                }

            }
            // Ajustar el ancho de las columnas al contenido
            $columns = [];
            for ($i = 0; $i < 31; $i++) { // Adjust the number (31) for your required range
                $columns[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            }
            
            foreach ($columns as $column) {
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

    public function generatePdf($id)
    {
        $this->reportData = ModelsReport::with(['equipment.components.fields.value','equipment.components.componentType'])->findOrFail($id);
        $components = $this->reportData->equipment->components;
        $this->reportData->diagnosis = json_decode($this->reportData->diagnosis,true);
        // $this->reportData->recommendations = json_decode($this->reportData->recommendations,true);
        
        $pdf = Pdf::loadView('livewire.report.report-pdf', [
            'reportData' => $this->reportData,
            'components' => $components
        ]);
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
                
                Mail::send([], [], function ($message) use ($pdfPath,$recipients) {
                    $message->to($recipients)
                            ->subject('Envio de reporte')
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
                }
            }else{
                $this->recommendations = [];
                $this->reset(['location','process','equipmentType']);
            }
        }else{
            $this->recommendations = [];
            $this->reset(['location','process','equipmentType']);
        }
    }

    public function handleSelectChange($value, $field)
    {
        if ($value === 'add_new') {
            $this->dispatch('open-modal-select', $field);
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

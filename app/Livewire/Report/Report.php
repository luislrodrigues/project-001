<?php

namespace App\Livewire\Report;

use App\Models\Equipment;
use App\Models\Report as ModelsReport;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Report extends Component
{

    public $equipment_search,$condition_search,$priority_search,$date_search,$reportIdToUpdate;
    public $equipment,$location,$process,$diagnosis,$measurement_date,$condition,$failure_mechanism,$component_failure,$recommendation,$priority,$ot_created,$status_ot,$execution_date,$verification_recommendation,$analist;


    public function render()
    {
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
        if (!empty($this->date_search)) {
            $reports->where('measurement_date', $this->date_search);
         }
        $equipments = Equipment::where('status','active')->get();

        $reports =$reports->paginate(10); 
        return view('livewire.report.report', [
            'reports'   =>$reports,
            'equipments'   =>$equipments
        ])->layout('layouts.app');
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
                'location'            => 'required|string|max:255',
                'process'             => 'required|string|max:255',
                'diagnosis'           => 'required',
                'condition'           => 'required',
                'failure_mechanism'   => 'required',
                'component_failure'   => 'required',
                'recommendation'      => 'required',
                'priority'            => 'required',
                'analist'             => 'required',
            ];
            $this->validate($rules);
            try {
                ModelsReport::create([
                    'equipment_id'                => $this->equipment,
                    'measurement_date'            => $this->measurement_date,
                    'location'                    => $this->location,
                    'process'                     => $this->process,
                    'diagnosis'                   => $this->diagnosis,
                    'condition'                   => $this->condition,
                    'priority'                    => $this->priority,
                    'failure_mechanism'           => $this->failure_mechanism,
                    'component_failure'           => $this->component_failure,
                    'recommendation'              => $this->recommendation,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'verification_recommendation' => $this->verification_recommendation,
                    'analist'                     => $this->analist,
                ]);
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
        $report = ModelsReport::findOrFail($reportId);
        $this->equipment = $report->equipment_id;
        $this->measurement_date = $report->measurement_date;
        $this->location = $report->location;
        $this->process = $report->process;
        $this->diagnosis = $report->diagnosis;
        $this->condition = $report->condition;
        $this->priority = $report->priority;
        $this->failure_mechanism = $report->failure_mechanism;
        $this->component_failure = $report->component_failure;
        $this->recommendation = $report->recommendation;
        $this->ot_created = $report->ot_created;
        $this->status_ot = $report->status_ot;
        $this->execution_date = $report->execution_date;
        $this->verification_recommendation = $report->verification_recommendation;
        $this->analist = $report->analist;
        $this->reportIdToUpdate = $reportId;
        $this->dispatch('open-modal-edit-report');
    }

    
    
    public function updateReport()
    {

        if (auth()->user()->can('edit report')) {
            $rules = [
                'equipment'           => 'required|integer|exists:equipment,id',
                'measurement_date'    => 'required|date',
                'location'            => 'required|string|max:255',
                'process'             => 'required|string|max:255',
                'diagnosis'           => 'required',
                'condition'           => 'required',
                'failure_mechanism'   => 'required',
                'component_failure'   => 'required',
                'recommendation'      => 'required',
                'priority'            => 'required',
                'analist'             => 'required',
            ];
            $this->validate($rules);
            try{
    
                $report = ModelsReport::findOrFail($this->reportIdToUpdate);
    
                $report->update([
                    'equipment_id'                => $this->equipment,
                    'measurement_date'            => $this->measurement_date,
                    'location'                    => $this->location,
                    'process'                     => $this->process,
                    'diagnosis'                   => $this->diagnosis,
                    'condition'                   => $this->condition,
                    'priority'                    => $this->priority,
                    'failure_mechanism'           => $this->failure_mechanism,
                    'component_failure'           => $this->component_failure,
                    'recommendation'              => $this->recommendation,
                    'ot_created'                  => $this->ot_created,
                    'status_ot'                   => $this->status_ot,
                    'execution_date'              => $this->execution_date,
                    'verification_recommendation' => $this->verification_recommendation,
                    'analist'                     => $this->analist,
                ]);
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
        $this->reset(['reportIdToUpdate','equipment','location','process','diagnosis','measurement_date','condition','failure_mechanism','component_failure','recommendation','priority','ot_created','status_ot','execution_date','verification_recommendation','analist']);
        $this->resetValidation();
    }
}

<?php

namespace App\Livewire\Equipment;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Equipment as ModelsEquipment;
use Livewire\Component;
use Livewire\WithPagination;

class Equipment extends Component
{
    use WithPagination;
    public  $search,$company_search, $status,$name,$company,$code,$tag,$description,$brand,$equipmentIdToUpdate;

    public function render()
    {
       $companies = Company::whereNull('deleted_at')->where('status','active')->get();
       $brands = Brand::whereNull('deleted_at')->where('status','active')->get();

        $equipments = ModelsEquipment::query();
        if (!empty($this->company_search)) {
           $equipments->where('company_id', $this->company_search);
        }
        if (!empty($this->search)) {
           $equipments->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
       $equipments =$equipments->paginate(10); 
        return view('livewire.equipment.equipment', [
            'equipments'       =>$equipments,
            'companies'        =>$companies,
            'brands'           =>$brands,
        ])->layout('layouts.app');
    }

    protected $listeners = ['destroy'];
    public function destroy($id)
    {
        if (auth()->user()->can('delete equipment')) {
            try{
                $equipment = ModelsEquipment::findOrFail($id);
                $equipment->status = 'inactive';
                $equipment->save();
                $equipment->delete();
                $this->dispatch('alertSuccess', 'El equipo se elimino correctamente.');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al aliminar el equipo: ' . $e->getMessage());
            }

        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    public function saveEquipment()
    {

        if (auth()->user()->can('save equipment')) {
            $rules = [
                'name'         => 'required|string|max:255',
                'code'         => 'required|string|max:255',
                'tag'          => 'required|string|max:255',
                'description'  => 'required|string|max:255',
                'company'      => 'required|integer|exists:companies,id',
            ];
            $this->validate($rules);
            try {
               ModelsEquipment::create([
                    'name'               => $this->name,
                    'code'               => $this->code,
                    'tag'                => $this->tag,
                    'description'        => $this->description,
                    'company_id'         => $this->company,
                ]);
    
                $this->resetForm();
                $this->dispatch('close-modal-equipment');
                $this->dispatch('alertSuccess','Se guardo el equipo correctamente');
            } catch (\Exception $e) {
                    $this->dispatch('alertFailed', 'Error al guardar el equipo: ' . $e->getMessage());
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    public function loadEquipment($equipmentId){
        $equipment = ModelsEquipment::findOrFail($equipmentId);

        if($equipment){
            $this->equipmentIdToUpdate = $equipment->id;
            $this->name = $equipment->name;
            $this->code = $equipment->code;
            $this->tag = $equipment->tag;
            $this->description = $equipment->description;
            $this->company = $equipment->company_id;
        }
    }
    public function showEquipmentDetails($equipmentId){
        $this->loadEquipment($equipmentId);
        $this->dispatch('open-modal-show-equipment');
    }
    public function OpenModalEdit($equipmentId){
        $this->loadEquipment($equipmentId);
        $this->dispatch('open-modal-edit-equipment');
    }


    public function updateEquipment()
    {
        if (auth()->user()->can('edit equipment')) {
            $rules = [
                'name'    => 'required|string|max:255',
                'code'    => 'required|string|max:255',
                'tag'    => 'required|string|max:255',
                'description'    => 'required|string|max:255',
                'company' => 'required|integer|exists:companies,id',
            ];
            $this->validate($rules);
            try {
                $equipment = ModelsEquipment::find($this->equipmentIdToUpdate);
                $equipment->update([
                    'name'               => $this->name,
                    'code'               => $this->code,
                    'tag'                => $this->tag,
                    'description'        => $this->description,
                    'company_id'         => $this->company,
                ]);
                $this->resetForm();
                $this->dispatch('close-modal-equipment');
                $this->dispatch('alertSuccess', 'Equipo actualizado correctamente');
            } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al actualizar el equipo: ' . $e->getMessage());
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    public function resetForm()
    {
        $this->reset(['name','company','code','tag','description', 'equipmentIdToUpdate','brand']);
        $this->resetValidation();
    }
}

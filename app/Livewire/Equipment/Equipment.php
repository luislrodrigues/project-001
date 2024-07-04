<?php

namespace App\Livewire\Equipment;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Equipment as ModelsEquipment;
use App\Models\EquipmentType;
use App\Models\EquipmentTypeField;
use App\Models\EquipmentTypeValue;
use Livewire\Component;
use Livewire\WithPagination;

class Equipment extends Component
{
    use WithPagination;
    public  $search,$company_search,$brand_search,$equipment_types_search, $status,$name,$company,$code,$tag,$brand,$type,$equipmentIdToUpdate;
    public $additionalFields  = [];
    public $fieldValues = [];
        public function mount()
    {
        $this->type = null;
    }
    public function render()
    {
       $companies = Company::whereNull('deleted_at')->where('status','active')->get();
       $brands = Brand::whereNull('deleted_at')->where('status','active')->get();
       $equipment_types = EquipmentType::whereNull('deleted_at')->where('status','active')->get();

        $equipments = ModelsEquipment::query();
        if (!empty($this->company_search)) {
           $equipments->where('company_id', $this->company_search);
        }
        if (!empty($this->brand_search)) {
           $equipments->where('brand_id', $this->brand_search);
        }
        if (!empty($this->equipment_types_search)) {
           $equipments->where('equipment_type_id', $this->equipment_types_search);
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
            'equipment_types' =>$equipment_types,
        ])->layout('layouts.app');
    }

    public function updatedType($value)
    {
        $this->type = $value;
        $this->additionalFields = EquipmentTypeField::where('equipment_type_id', $this->type)->get();
        foreach ($this->additionalFields as $field) {
            if ($field->type == 'select') {
                $field->options = json_decode($field->options);
            }
        }
        $this->fieldValues = [];
    }

    protected $listeners = ['destroy'];
    public function destroy($id)
    {
        if (auth()->user()->can('delete equipment')) {
            try{
                $equipment = ModelsEquipment::findOrFail($id);
                EquipmentTypeValue::where('id_equipment', $id)->delete();
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
                'name'    => 'required|string|max:255',
                'code'    => 'required|string|max:255',
                'tag'    => 'required|string|max:255',
                'company' => 'required|integer|exists:companies,id',
                'brand'   => 'required|integer|exists:brands,id',
                'type'    => 'required|integer|exists:equipment_types,id',
            ];
            foreach ($this->additionalFields as $field) {
                if ($field->type == 'text') {
                    $rules["fieldValues.{$field->id}"] = 'required|string';
                }else if($field->type == 'select'){
                    $rules["fieldValues.{$field->id}"] = 'required';
                }else if($field->type == 'numeric'){
                    $rules["fieldValues.{$field->id}"] = 'required|numeric';
                }
            }
            $this->validate($rules);
            try {
               $equipment = ModelsEquipment::create([
                    'name'               => $this->name,
                    'code'               => $this->code,
                    'tag'                => $this->tag,
                    'company_id'         => $this->company,
                    'brand_id'           => $this->brand,
                    'equipment_type_id'  => $this->type,
                ]);
    
                foreach ($this->fieldValues as $typeId => $value) {
                    EquipmentTypeValue::create([
                        'id_equipment'            => $equipment->id,
                        'equipment_type_field_id' => $typeId,
                        'value'                   => $value,
                    ]);
                }
    
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
            $this->company = $equipment->company_id;
            $this->brand = $equipment->brand_id;
            $this->type = $equipment->equipment_type_id;
            $this->updatedType($equipment->equipment_type_id);
    
            $equipmentTypeValues = EquipmentTypeValue::where('id_equipment', $equipment->id)->get();
    
            foreach ($equipmentTypeValues as $value) {
                $this->fieldValues[$value->equipment_type_field_id] = $value->value;
            }

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
                'company' => 'required|integer|exists:companies,id',
                'brand'   => 'required|integer|exists:brands,id',
                'type'    => 'required|integer|exists:equipment_types,id',
            ];
            foreach ($this->additionalFields as $field) {
                if ($field->type == 'text') {
                    $rules["fieldValues.{$field->id}"] = 'required|string';
                } else if ($field->type == 'select') {
                    $rules["fieldValues.{$field->id}"] = 'required';
                } else if ($field->type == 'numeric') {
                    $rules["fieldValues.{$field->id}"] = 'required|numeric';
                }
            }
            $this->validate($rules);
    
            try {
                $equipment = ModelsEquipment::find($this->equipmentIdToUpdate);
                $equipment->update([
                    'name'               => $this->name,
                    'code'               => $this->code,
                    'tag'               => $this->tag,
                    'company_id'         => $this->company,
                    'brand_id'           => $this->brand,
                    'equipment_type_id'  => $this->type,
                ]);
                EquipmentTypeValue::where('id_equipment', $equipment->id)->delete();
                foreach ($this->fieldValues as $typeId => $value) {
                    EquipmentTypeValue::updateOrCreate(
                        ['id_equipment' => $equipment->id, 'equipment_type_field_id' => $typeId],
                        ['value' => $value]
                    );
                }
    
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
        $this->reset(['name','company','code','tag','type', 'equipmentIdToUpdate','type','brand']);
        $this->additionalFields = [];
        $this->resetValidation();
    }
}

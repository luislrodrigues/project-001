<?php

namespace App\Livewire\ComponentManager;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Component as ModelsComponent;
use App\Models\ComponentType;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeValue;
use App\Models\Equipment;
use Livewire\Component;

class ComponentManager extends Component
{

    public $type_search,$equipment_search,$company_search,$brand_search;
    public $componentIdToUpdate,$component_type,$equipment,$brand;
    public $additionalFields = [];
    public $fieldValues = [];
    public function render()
    {
        $equipments = Equipment::where('status','active')->get();
        $component_types = ComponentType::where('status','active')->get();
        $companies = Company::where('status','active')->get();
        $brands = Brand::where('status','active')->get();
        $components = ModelsComponent::query();
        if (!empty($this->type_search)) {
            $components->where('component_type_id', $this->type_search);
        }
        if (!empty($this->equipment_search)) {
            $components->where('equipment_id', $this->equipment_search);
        }
        if (!empty($this->brand_search)) {
            $components->where('brand_id', $this->brand_search);
        }
        if (!empty($this->company_search)) {
            $components->whereHas('equipment', function ($query) {
                $query->where('company_id', $this->company_search);
            });
         }
        $components =$components->paginate(10); 

        return view('livewire.component-manager.component-manager', [
            'equipments'   =>$equipments,
            'component_types'   =>$component_types,
            'companies'   =>$companies,
            'components'   =>$components,
            'brands'   =>$brands,
        ])
        ->layout('layouts.app');
    }

    public function editComponent($id){
        $this->loadComponent($id);
        $this->dispatch('open-modal-edit-component');
    }
    public function showComponentDetails($id){
        $this->loadComponent($id);
        $this->dispatch('open-modal-show-component');
    }

    public function loadComponent($componentId){
        $component = ModelsComponent::findOrFail($componentId);

        if($component){
            $this->componentIdToUpdate = $component->id;
            $this->equipment = $component->equipment_id;
            $this->brand = $component->brand_id;
            $this->component_type = $component->component_type_id;
            $this->updatedComponentType($component->component_type_id);
    
            $componentTypeValues = ComponentTypeValue::where('id_component', $component->id)->get();
    
            foreach ($componentTypeValues as $value) {
                $this->fieldValues[$value->component_type_field_id] = $value->value;
            }
         }
    }
    public function saveComponent(){

        if (auth()->user()->can('save equipment')) {
            $rules = [
                'equipment'       => 'required|integer|exists:equipment,id',
                'component_type'  => 'required|integer|exists:component_types,id',
                'brand'  => 'required|integer|exists:brands,id',
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
                  $component = ModelsComponent::create([
                    "equipment_id"      => $this->equipment,
                    "component_type_id" => $this->component_type,
                    "brand_id"          => $this->brand,
                ]);
                foreach ($this->fieldValues as $typeId => $value) {
                    ComponentTypeValue::create([
                        'id_component'            => $component->id,
                        'component_type_field_id' => $typeId,
                        'value'                   => $value,
                    ]);
                }
                $this->resetForm();
                $this->dispatch('close-modal-component');
                $this->dispatch('alertSuccess','Se guardo el equipo correctamente');
            } catch (\Exception $e) {
                    $this->dispatch('alertFailed', 'Error al guardar el equipo: ' . $e->getMessage());
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }

    }
    public function updateComponent(){
        if (auth()->user()->can('edit equipment')) {
            $rules = [
                'equipment'       => 'required|integer|exists:equipment,id',
                'component_type'  => 'required|integer|exists:component_types,id',
                'brand'  => 'required|integer|exists:brands,id',
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
                $component = ModelsComponent::find($this->componentIdToUpdate);
                $component->update([
                    "equipment_id"      => $this->equipment,
                    "component_type_id" => $this->component_type,
                    "brand_id"          => $this->brand,
                ]);
                ComponentTypeValue::where('id_component', $component->id)->delete();
                foreach ($this->fieldValues as $typeId => $value) {
                    ComponentTypeValue::updateOrCreate(
                        ['id_component' => $component->id, 'component_type_field_id' => $typeId],
                        ['value' => $value]
                    );
                    ComponentTypeValue::create([
                        'id_component'            => $component->id,
                        'component_type_field_id' => $typeId,
                        'value'                   => $value,
                    ]);
                }
    
                $this->resetForm();
                $this->dispatch('close-modal-component');
                $this->dispatch('alertSuccess', 'Componente actualizado correctamente');
            } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al actualizar el Componente: ' . $e->getMessage());
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    public function updatedComponentType($value)
    {
        $this->component_type = $value;
        $this->additionalFields = ComponentTypeField::where('component_type_id', $this->component_type)->get();
        foreach ($this->additionalFields as $field) {
            if ($field->type == 'select') {
                $field->options = json_decode($field->options);
            }
        }
        $this->fieldValues = [];
    }

    public function resetForm(){
        $this->additionalFields = [];
        $this->fieldValues = [];
        $this->reset(['componentIdToUpdate','component_type','equipment','brand']);
        $this->resetValidation();
    }
    protected $listeners = ['destroy'];
    public function destroy($id)
    {
        if (auth()->user()->can('delete equipment')) {
            try{
                $component = ModelsComponent::findOrFail($id);
                $component->status = 'inactive';
                $component->save();
                $component->delete();
                $this->dispatch('alertSuccess', 'El componente se elimino correctamente.');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al aliminar el componente: ' . $e->getMessage());
            }

        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }
}

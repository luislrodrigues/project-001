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
use Livewire\WithPagination;

class ComponentManager extends Component
{
    use WithPagination;
    public $type_search,$equipment_search,$company_search,$brand_search;
    public $componentIdToUpdate,$component_type,$equipment,$brand,$name_equipment,$name_type_component,$name_brand;
    public $additionalFields = [];
    public $fieldValues = [];
    public $brands = [];


        public function mount()
    {
        $this->brands = Brand::where('status','active')->get();

    }
    public function render()
    {
        $equipments = Equipment::where('status','active')->get();
        $component_types = ComponentType::where('status','active')->get();
        $companies = Company::where('status','active')->get();
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
            'equipments'       =>$equipments,
            'component_types'  =>$component_types,
            'companies'        =>$companies,
            'components'       =>$components,
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
            $this->name_equipment= $component->equipment->name;
            $this->name_type_component= $component->componentType->name;
            $this->name_brand= $component->brand->name;
            $this->componentIdToUpdate = $component->id;
            $this->equipment = $component->equipment_id;
            $this->brand = $component->brand_id;
            $this->component_type = $component->component_type_id;
            $this->updatedComponentType($component->component_type_id);
    
            // Cargar valores del componente (corregido: usar component_id)
            $componentTypeValues = ComponentTypeValue::where('component_id', $component->id)->get();
    
            foreach ($componentTypeValues as $value) {
                // Si tiene opción asociada, usar el value de la opción, sino el valor directo
                $this->fieldValues[$value->component_type_field_id] = $value->option_id 
                    ? $value->option->value 
                    : $value->value;
            }
         }
    }
    public function saveComponent(){
        if (auth()->user()->can('save equipment')) {
            if($this->brand == 'add_new') $this->brand = "";
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
                    if (empty($value)) continue; // Saltar valores vacíos
                    
                    $field = ComponentTypeField::find($typeId);
                    $optionId = null;
                    $storedValue = $value;
                    
                    // Si el campo tiene opciones, buscar si el valor corresponde a una opción
                    if ($field && $field->hasOptions()) {
                        $option = $field->options()->where('value', $value)->first();
                        if ($option) {
                            $optionId = $option->id;
                            $storedValue = $option->value;
                        }
                    }
                    
                    ComponentTypeValue::create([
                        'component_id'            => $component->id,
                        'component_type_field_id' => $typeId,
                        'option_id'               => $optionId,
                        'value'                   => $storedValue,
                        'raw_value'               => $value, // Guardar valor original
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
                // Eliminar valores antiguos y crear nuevos
                ComponentTypeValue::where('component_id', $component->id)->delete();
                
                foreach ($this->fieldValues as $typeId => $value) {
                    if (empty($value)) continue; // Saltar valores vacíos
                    
                    $field = ComponentTypeField::find($typeId);
                    $optionId = null;
                    $storedValue = $value;
                    
                    // Si el campo tiene opciones, buscar si el valor corresponde a una opción
                    if ($field && $field->hasOptions()) {
                        $option = $field->options()->where('value', $value)->first();
                        if ($option) {
                            $optionId = $option->id;
                            $storedValue = $option->value;
                        }
                    }
                    
                    ComponentTypeValue::create([
                        'component_id'            => $component->id,
                        'component_type_field_id' => $typeId,
                        'option_id'               => $optionId,
                        'value'                   => $storedValue,
                        'raw_value'               => $value,
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
        // Cargar campos con sus opciones (nuevo sistema)
        $this->additionalFields = ComponentTypeField::with('options')
            ->where('component_type_id', $this->component_type)
            ->ordered()
            ->get();
        
        // Para compatibilidad: asegurar que opciones estén disponibles
        foreach ($this->additionalFields as $field) {
            if (in_array($field->type, ['select', 'multiselect'])) {
                // Usar nuevas opciones si existen, sino usar formato antiguo
                if ($field->options->count() > 0) {
                    $field->optionsArray = $field->options->pluck('value')->toArray();
                } elseif (!empty($field->options)) {
                    // Fallback a formato antiguo
                    $field->optionsArray = json_decode($field->options, true) ?? [];
                } else {
                    $field->optionsArray = [];
                }
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
    protected $listeners = ['destroy','updatedBrands','item-saved'];

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

    // Método para abrir el modal de gestión (si se necesita desde algún lugar)
    public function openManageModal($field)
    {
        $this->dispatch('open-modal-select', $field);
    }
    public function itemSaved($data)
    {
        $field = $data['field'] ?? null;
        $id = $data['id'] ?? null;

        if ($field && $id) {
            // Actualizar la propiedad correspondiente con el ID del nuevo registro
            switch ($field) {
                case 'brand':
                    $this->brand = $id;
                    break;
            }
        }
    }
    public function updatedBrands()
    {
        $this->brands = Brand::where('status', 'active')->get();
        $this->dispatch('updateOptions', $this->brands);
    }
}

<?php

namespace App\Livewire\ComponentType;

use App\Models\Brand;
use App\Models\ComponentType as ModelsComponentType;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;


class ComponentType extends Component
{
    use WithPagination;

    public $search, $brand_search,$IdToUpdate,$name;
    public $fields = [];
    public $showOptionsManager = false;
    public $currentFieldIndex = null;
    public $tempOptions = []; // Opciones temporales por campo
    public $newTempOption = [
        'label' => '',
        'value' => '',
        'is_default' => false,
    ];
    
    protected $listeners = ['destroy', 'openOptionsManager'];
    
    public function openOptionsManager($fieldId)
    {
        // El evento será manejado por el componente FieldOptionsManager
        // No necesitamos hacer nada aquí, solo permitir que el evento pase
    }
    public function render()
    {
        $brands = Brand::where('status','active')->get();
        $type_components = ModelsComponentType::where('status','active'); 

        if (!empty($this->brand_search)) {
            $type_components->where('brand_id', $this->brand_search);
         }
         if (!empty($this->search)) {
            $type_components->where(function ($query) {
                 $query->where('name', 'like', '%' . $this->search . '%');
             });
         }
        $type_components =$type_components->paginate(10); 
         return view('livewire.component-type.component-type',[
            'brands' => $brands,
            'type_components' => $type_components,
         ])
        ->layout('layouts.app');;
    }

    public function saveTypeComponent(){

        if (auth()->user()->can('save equipment')) {
            if(count($this->fields) > 0){
                        // Ya no necesitamos sincronizar, las opciones temporales se usan directamente
                
                foreach ($this->fields as $index => $field) {
                    $rules = [
                        "fields.$index.name" => 'required|string|max:255',
                        "fields.$index.type" => 'required|string|max:255',
                    ];
                    if(in_array($field['type'], ['select', 'multiselect'])){
                        // Validar que tenga opciones temporales
                        if (empty($this->tempOptions[$index]) || count($this->tempOptions[$index]) === 0) {
                            $this->addError("fields.$index.options", "Debes agregar al menos una opción para campos tipo select/multiselect");
                        }
                    }
                    $this->validate($rules);
                }
                try {
                    $type_component = ModelsComponentType::create([
                        'name'               => $this->name,
                    ]);
                    foreach ($this->fields as $fieldIndex => $field) {
                        $createdField = $type_component->componentTypeFields()->create([
                            'name' => $field['name'],
                            'type' => $field['type'],
                            'unit' => $field['unit'] ?? null,
                            'display_order' => $fieldIndex + 1,
                            'is_required' => $field['is_required'] ?? false,
                            'options' => null, // Ya no se usa, solo opciones en tabla separada
                        ]);
                        
                        // Crear opciones en nueva tabla si es select/multiselect
                        if (in_array($field['type'], ['select', 'multiselect']) && !empty($this->tempOptions[$fieldIndex])) {
                            // Usar opciones temporales (tienen label, value, is_default, etc.)
                            foreach ($this->tempOptions[$fieldIndex] as $tempOption) {
                                $createdField->options()->create([
                                    'label' => $tempOption['label'],
                                    'value' => $tempOption['value'],
                                    'display_order' => $tempOption['display_order'],
                                    'is_default' => $tempOption['is_default'] ?? false,
                                    'status' => 'active',
                                ]);
                            }
                        }
                    }
                    // Limpiar opciones temporales después de guardar
                    $this->tempOptions = [];
                    $this->resetForm();
                    $this->dispatch('close-modal-type-component');
                    $this->dispatch('alertSuccess','Se guardo correctamente');
                } catch (\Exception $e) {
                        $this->dispatch('alertFailed', 'Error al guardar: ' . $e->getMessage());
                }
            }else{
                $this->dispatch('alertInfo', 'Debes agregar un atributo.');
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }

    }
    public function updateTypeComponent(){

        if (auth()->user()->can('save equipment')) {
            if(count($this->fields) > 0){
                foreach ($this->fields as $index => $field) {
                    $rules = [
                        "fields.$index.name" => 'required|string|max:255',
                        "fields.$index.type" => 'required|string|max:255',
                    ];
                    if($field['type'] == 'select'){
                        $rules["fields.$index.options"] = 'required|string';
                    }
                    $this->validate($rules);
                }
                try {
                    $type_component = ModelsComponentType::findOrFail($this->IdToUpdate);

                    $type_component->update([
                        'name'               => $this->name,
                    ]);
                    foreach ($this->fields as $fieldIndex => $field) {
                        if(isset($field['id'])){
                            // Actualizar campo existente
                            $existingField = $type_component->componentTypeFields()->find($field['id']);
                            if ($existingField) {
                                $existingField->update([
                                    'name' => $field['name'],
                                    'type' => $field['type'],
                                    'unit' => $field['unit'] ?? null,
                                    'display_order' => $fieldIndex + 1,
                                    'is_required' => $field['is_required'] ?? false,
                                    'options' => null,
                                ]);
                                
                                // Actualizar opciones desde tempOptions si es select/multiselect
                                if (in_array($field['type'], ['select', 'multiselect']) && !empty($this->tempOptions[$fieldIndex])) {
                                    // Obtener valores actuales de opciones
                                    $currentOptionValues = $existingField->allOptions()->pluck('value')->toArray();
                                    $newOptionValues = array_column($this->tempOptions[$fieldIndex], 'value');
                                    
                                    // Eliminar opciones que ya no están (solo si no están en uso)
                                    $optionsToDelete = array_diff($currentOptionValues, $newOptionValues);
                                    if (!empty($optionsToDelete)) {
                                        $existingField->allOptions()
                                            ->whereIn('value', $optionsToDelete)
                                            ->whereDoesntHave('componentValues')
                                            ->delete();
                                    }
                                    
                                    // Crear/actualizar opciones desde tempOptions
                                    foreach ($this->tempOptions[$fieldIndex] as $tempOption) {
                                        $existingField->options()->updateOrCreate(
                                            ['value' => $tempOption['value']],
                                            [
                                                'label' => $tempOption['label'],
                                                'display_order' => $tempOption['display_order'],
                                                'is_default' => $tempOption['is_default'] ?? false,
                                                'status' => 'active',
                                            ]
                                        );
                                    }
                                }
                            }
                        }else{
                            // Crear nuevo campo
                            $createdField = $type_component->componentTypeFields()->create([
                                'name' => $field['name'],
                                'type' => $field['type'],
                                'unit' => $field['unit'] ?? null,
                                'display_order' => $fieldIndex + 1,
                                'is_required' => $field['is_required'] ?? false,
                                'options' => null,
                            ]);
                            
                            // Crear opciones desde tempOptions si es select/multiselect
                            if (in_array($field['type'], ['select', 'multiselect']) && !empty($this->tempOptions[$fieldIndex])) {
                                foreach ($this->tempOptions[$fieldIndex] as $tempOption) {
                                    $createdField->options()->create([
                                        'label' => $tempOption['label'],
                                        'value' => $tempOption['value'],
                                        'display_order' => $tempOption['display_order'],
                                        'is_default' => $tempOption['is_default'] ?? false,
                                        'status' => 'active',
                                    ]);
                                }
                            }
                        }
                    }
                    $this->resetForm();
                    $this->dispatch('close-modal-type-component');
                    $this->dispatch('alertSuccess','Se actualizo correctamente');
                } catch (\Exception $e) {
                        $this->dispatch('alertFailed', 'Error al actualizar: ' . $e->getMessage());
                }
            }else{
                $this->dispatch('alertInfo', 'Debes agregar un atributo.');
            }
 
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }

    }

    public function destroy($id){
        if (auth()->user()->can('delete equipment')) {
            try{
                $type_component = ModelsComponentType::findOrFail($id);
                $type_component->status = 'inactive';
                $type_component->save();
                $type_component->delete();
                $type_component->componentTypeFields()->delete();
                $this->dispatch('alertSuccess', 'Se elimino correctamente.');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al aliminar: ' . $e->getMessage());
            }
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    public function addField()
    {
        $newIndex = count($this->fields);
        $this->fields[] = [
            'name' => '', 
            'type' => '', 
            'unit' => '',
        ];
        // Inicializar array de opciones temporales para este campo si no existe
        if (!isset($this->tempOptions[$newIndex])) {
            $this->tempOptions[$newIndex] = [];
        }
    }
    
    public function openTempOptionsManager($index)
    {
        // Para campos guardados, necesitamos usar el ID del campo
        // Para campos nuevos, usamos el índice
        $field = $this->fields[$index] ?? null;
        
        if ($field && isset($field['id']) && !empty($field['id'])) {
            // Si el campo ya está guardado, usar el gestor permanente
            $this->dispatch('openOptionsManager', ['fieldId' => $field['id']]);
            return;
        }
        
        // Campo nuevo, usar gestor temporal
        $this->currentFieldIndex = $index;
        if (!isset($this->tempOptions[$index])) {
            $this->tempOptions[$index] = [];
        }
        $this->showOptionsManager = true;
    }
    
    public function closeTempOptionsManager()
    {
        $this->showOptionsManager = false;
        $this->currentFieldIndex = null;
    }
    
    public function addTempOption($label = null, $value = null, $isDefault = null)
    {
        if ($this->currentFieldIndex === null) {
            return;
        }
        
        // Usar valores del formulario si no se pasan como parámetros
        $label = $label ?? $this->newTempOption['label'] ?? '';
        $value = $value ?? $this->newTempOption['value'] ?? '';
        $isDefault = $isDefault ?? ($this->newTempOption['is_default'] ?? false);
        
        if (empty($label) || empty($value)) {
            $this->dispatch('alertFailed', 'El nombre y el valor son requeridos');
            return;
        }
        
        if (!isset($this->tempOptions[$this->currentFieldIndex])) {
            $this->tempOptions[$this->currentFieldIndex] = [];
        }
        
        // Verificar que el valor no esté duplicado
        foreach ($this->tempOptions[$this->currentFieldIndex] as $opt) {
            if ($opt['value'] === $value) {
                $this->dispatch('alertFailed', 'Este valor ya existe');
                return;
            }
        }
        
        // Si se marca como default, desmarcar otros
        if ($isDefault) {
            foreach ($this->tempOptions[$this->currentFieldIndex] as &$opt) {
                $opt['is_default'] = false;
            }
        }
        
        $maxOrder = count($this->tempOptions[$this->currentFieldIndex]);
        $this->tempOptions[$this->currentFieldIndex][] = [
            'label' => $label,
            'value' => $value,
            'is_default' => $isDefault,
            'display_order' => $maxOrder + 1,
        ];
        
        // Limpiar formulario
        $this->newTempOption = [
            'label' => '',
            'value' => '',
            'is_default' => false,
        ];
        
        // Actualizar el campo de opciones con formato punto y coma
        $this->updateFieldOptionsFromTemp();
        $this->dispatch('alertSuccess', 'Opción agregada');
    }
    
    public function removeTempOption($index)
    {
        if ($this->currentFieldIndex === null || !isset($this->tempOptions[$this->currentFieldIndex][$index])) {
            return;
        }
        
        unset($this->tempOptions[$this->currentFieldIndex][$index]);
        $this->tempOptions[$this->currentFieldIndex] = array_values($this->tempOptions[$this->currentFieldIndex]);
        
        // Reordenar
        foreach ($this->tempOptions[$this->currentFieldIndex] as $order => &$opt) {
            $opt['display_order'] = $order + 1;
        }
        
        $this->updateFieldOptionsFromTemp();
    }
    
    public function updateTempOption($index, $label, $value, $isDefault = null)
    {
        if ($this->currentFieldIndex === null || !isset($this->tempOptions[$this->currentFieldIndex][$index])) {
            return;
        }
        
        $this->tempOptions[$this->currentFieldIndex][$index]['label'] = $label;
        $this->tempOptions[$this->currentFieldIndex][$index]['value'] = $value;
        
        if ($isDefault !== null) {
            if ($isDefault) {
                // Desmarcar otros
                foreach ($this->tempOptions[$this->currentFieldIndex] as $i => &$opt) {
                    $opt['is_default'] = ($i === $index);
                }
            } else {
                $this->tempOptions[$this->currentFieldIndex][$index]['is_default'] = false;
            }
        }
        
        $this->updateFieldOptionsFromTemp();
    }
    
    public function moveTempOptionUp($index)
    {
        if ($this->currentFieldIndex === null || $index === 0) {
            return;
        }
        
        $options = &$this->tempOptions[$this->currentFieldIndex];
        [$options[$index - 1], $options[$index]] = [$options[$index], $options[$index - 1]];
        
        // Actualizar orden
        foreach ($options as $order => &$opt) {
            $opt['display_order'] = $order + 1;
        }
        
        $this->updateFieldOptionsFromTemp();
    }
    
    public function moveTempOptionDown($index)
    {
        if ($this->currentFieldIndex === null || !isset($this->tempOptions[$this->currentFieldIndex][$index + 1])) {
            return;
        }
        
        $options = &$this->tempOptions[$this->currentFieldIndex];
        [$options[$index], $options[$index + 1]] = [$options[$index + 1], $options[$index]];
        
        // Actualizar orden
        foreach ($options as $order => &$opt) {
            $opt['display_order'] = $order + 1;
        }
        
        $this->updateFieldOptionsFromTemp();
    }
    
    protected function updateFieldOptionsFromTemp()
    {
        if ($this->currentFieldIndex === null || !isset($this->tempOptions[$this->currentFieldIndex])) {
            return;
        }
        
        // Convertir opciones temporales a formato punto y coma
        $values = array_map(function($opt) {
            return $opt['value'];
        }, $this->tempOptions[$this->currentFieldIndex]);
        
        if (isset($this->fields[$this->currentFieldIndex])) {
            $this->fields[$this->currentFieldIndex]['options'] = implode(';', $values);
        }
    }
    
    public function syncTempOptionsToFields()
    {
        // Ya no es necesario sincronizar, las opciones temporales se usan directamente
        // Este método se mantiene por compatibilidad pero ya no hace nada
    }


    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function loadEquipment($id){
        $type_component = ModelsComponentType::with('componentTypeFields.allOptions')->findOrFail($id);
        if($type_component){
            $this->IdToUpdate = $type_component->id;
            $this->name = $type_component->name;
            $fieldsData = $type_component->componentTypeFields;
            $this->fields = [];
            $this->tempOptions = []; // Limpiar opciones temporales anteriores
            
            // Cargar campos con sus opciones
            foreach ($fieldsData as $fieldIndex => $fieldData) {
                $field = $fieldData->toArray();
                
                // Para campos select/multiselect, cargar opciones en tempOptions usando el índice del array
                if (in_array($field['type'], ['select', 'multiselect'])) {
                    $options = $fieldData->allOptions;
                    if ($options->count() > 0) {
                        // Usar el índice del array en lugar del ID para campos no guardados
                        $this->tempOptions[$fieldIndex] = $options->map(function($opt) {
                            return [
                                'label' => $opt->label,
                                'value' => $opt->value,
                                'is_default' => $opt->is_default,
                                'display_order' => $opt->display_order,
                            ];
                        })->sortBy('display_order')->values()->toArray();
                    }
                }
                
                $this->fields[] = $field;
            }
        }
    }

    public function OpenModalEdit($id){
        $this->loadEquipment($id);
        $this->dispatch('open-modal-edit-type-component');
    }


    public function resetForm()
    {
        $this->reset(['name']);
        $this->fields = [];
        $this->tempOptions = [];
        $this->showOptionsManager = false;
        $this->currentFieldIndex = null;
        $this->resetValidation();
    }
}

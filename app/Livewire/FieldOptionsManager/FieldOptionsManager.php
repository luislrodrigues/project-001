<?php

namespace App\Livewire\FieldOptionsManager;

use App\Models\ComponentTypeField;
use App\Models\ComponentTypeFieldOption;
use Livewire\Component;

class FieldOptionsManager extends Component
{
    public $fieldId;
    public $field;
    public $options = [];
    public $showModal = false;
    
    // Para agregar nueva opción
    public $newOption = [
        'label' => '',
        'value' => '',
        'is_default' => false,
        'color' => null,
    ];
    
    // Para editar opción
    public $editingOptionId = null;
    public $editingOption = [
        'label' => '',
        'value' => '',
        'is_default' => false,
        'color' => null,
        'display_order' => 0,
    ];

    protected $listeners = [
        'openOptionsManager' => 'handleOpenModal',
        'closeModal' => 'closeModal',
    ];
    
    public function handleOpenModal($data)
    {
        $fieldId = is_array($data) ? ($data['fieldId'] ?? $data) : $data;
        $this->openModal($fieldId);
    }

    public function mount($fieldId = null)
    {
        if ($fieldId) {
            $this->fieldId = $fieldId;
            $this->loadField();
        }
    }

    public function openModal($fieldId)
    {
        $this->fieldId = $fieldId;
        $this->loadField();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['newOption', 'editingOptionId', 'editingOption']);
    }

    public function loadField()
    {
        if (!$this->fieldId) {
            return;
        }

        $this->field = ComponentTypeField::with('allOptions')->findOrFail($this->fieldId);
        $this->options = $this->field->allOptions->sortBy('display_order')->values()->toArray();
    }

    public function addOption()
    {
        $this->validate([
            'newOption.label' => 'required|string|max:255',
            'newOption.value' => 'required|string|max:255',
        ], [
            'newOption.label.required' => 'El nombre es requerido',
            'newOption.value.required' => 'El valor es requerido',
        ], [
            'newOption.label' => 'nombre',
            'newOption.value' => 'valor',
        ]);

        // Verificar que el valor no exista ya
        $exists = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->where('value', $this->newOption['value'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            $this->addError('newOption.value', 'Este valor ya existe en las opciones');
            return;
        }

        // Obtener el siguiente orden
        $maxOrder = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->max('display_order') ?? 0;

        // Si se marca como default, desmarcar otros
        if ($this->newOption['is_default']) {
            ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
                ->update(['is_default' => false]);
        }

        ComponentTypeFieldOption::create([
            'component_type_field_id' => $this->fieldId,
            'label' => $this->newOption['label'],
            'value' => $this->newOption['value'],
            'display_order' => $maxOrder + 1,
            'is_default' => $this->newOption['is_default'] ?? false,
            'color' => $this->newOption['color'] ?: null,
            'status' => 'active',
        ]);

        $this->reset('newOption');
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción agregada correctamente');
    }

    public function editOption($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        $this->editingOptionId = $optionId;
        $this->editingOption = [
            'label' => $option->label,
            'value' => $option->value,
            'is_default' => $option->is_default,
            'color' => $option->color,
            'display_order' => $option->display_order,
        ];
    }

    public function updateOption()
    {
        $this->validate([
            'editingOption.label' => 'required|string|max:255',
            'editingOption.value' => 'required|string|max:255',
        ], [
            'editingOption.label.required' => 'El nombre es requerido',
            'editingOption.value.required' => 'El valor es requerido',
        ], [
            'editingOption.label' => 'nombre',
            'editingOption.value' => 'valor',
        ]);

        $option = ComponentTypeFieldOption::findOrFail($this->editingOptionId);

        // Verificar que el valor no exista en otra opción
        $exists = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->where('value', $this->editingOption['value'])
            ->where('id', '!=', $this->editingOptionId)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            $this->addError('editingOption.value', 'Este valor ya existe en otra opción');
            return;
        }

        // Si se marca como default, desmarcar otros
        if ($this->editingOption['is_default'] && !$option->is_default) {
            ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
                ->where('id', '!=', $this->editingOptionId)
                ->update(['is_default' => false]);
        }

        $option->update([
            'label' => $this->editingOption['label'],
            'value' => $this->editingOption['value'],
            'is_default' => $this->editingOption['is_default'] ?? false,
            'color' => $this->editingOption['color'] ?: null,
            'display_order' => $this->editingOption['display_order'],
        ]);

        $this->cancelEdit();
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción actualizada correctamente');
    }

    public function cancelEdit()
    {
        $this->reset(['editingOptionId', 'editingOption']);
    }

    public function deleteOption($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        
        // Verificar si la opción está en uso
        if ($option->isInUse()) {
            $this->dispatch('alertFailed', 'No se puede eliminar: la opción está siendo usada en componentes');
            return;
        }

        $option->delete();
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción eliminada correctamente');
    }

    public function toggleDefault($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        
        if ($option->is_default) {
            $option->update(['is_default' => false]);
            $this->dispatch('alertSuccess', 'Opción por defecto removida');
        } else {
            // Desmarcar otros defaults
            ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
                ->update(['is_default' => false]);
            
            $option->update(['is_default' => true]);
            $this->dispatch('alertSuccess', 'Opción establecida como predeterminada');
        }
        
        $this->loadField();
    }

    public function moveUp($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        $previousOption = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->where('display_order', '<', $option->display_order)
            ->orderBy('display_order', 'desc')
            ->first();

        if ($previousOption) {
            $tempOrder = $option->display_order;
            $option->update(['display_order' => $previousOption->display_order]);
            $previousOption->update(['display_order' => $tempOrder]);
            $this->loadField();
            $this->dispatch('alertSuccess', 'Orden actualizado');
        }
    }

    public function moveDown($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        $nextOption = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->where('display_order', '>', $option->display_order)
            ->orderBy('display_order', 'asc')
            ->first();

        if ($nextOption) {
            $tempOrder = $option->display_order;
            $option->update(['display_order' => $nextOption->display_order]);
            $nextOption->update(['display_order' => $tempOrder]);
            $this->loadField();
            $this->dispatch('alertSuccess', 'Orden actualizado');
        }
    }

    public function toggleStatus($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        $newStatus = $option->status === 'active' ? 'inactive' : 'active';
        
        // No permitir desactivar si es default
        if ($newStatus === 'inactive' && $option->is_default) {
            $this->dispatch('alertFailed', 'No se puede desactivar una opción que es predeterminada');
            return;
        }
        
        $option->update(['status' => $newStatus]);
        $this->loadField();
        $this->dispatch('alertSuccess', 'Estado actualizado');
    }

    public function render()
    {
        return view('livewire.field-options-manager.field-options-manager');
    }
}

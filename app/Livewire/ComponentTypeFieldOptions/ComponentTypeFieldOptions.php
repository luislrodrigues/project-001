<?php

namespace App\Livewire\ComponentTypeFieldOptions;

use App\Models\ComponentTypeField;
use App\Models\ComponentTypeFieldOption;
use Livewire\Component;

class ComponentTypeFieldOptions extends Component
{
    public $fieldId;
    public $field;
    public $options = [];
    public $newOption = [
        'label' => '',
        'value' => '',
        'display_order' => 0,
        'is_default' => false,
    ];

    public function mount($fieldId)
    {
        $this->fieldId = $fieldId;
        $this->loadField();
    }

    public function loadField()
    {
        $this->field = ComponentTypeField::with('allOptions')->findOrFail($this->fieldId);
        $this->options = $this->field->allOptions->toArray();
    }

    public function addOption()
    {
        $this->validate([
            'newOption.label' => 'required|string|max:255',
            'newOption.value' => 'required|string|max:255',
        ], [
            'newOption.label.required' => 'El label es requerido',
            'newOption.value.required' => 'El valor es requerido',
        ]);

        $maxOrder = ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
            ->max('display_order') ?? 0;

        ComponentTypeFieldOption::create([
            'component_type_field_id' => $this->fieldId,
            'label' => $this->newOption['label'],
            'value' => $this->newOption['value'],
            'display_order' => $maxOrder + 1,
            'is_default' => $this->newOption['is_default'] ?? false,
            'status' => 'active',
        ]);

        // Si se marca como default, desmarcar otros
        if ($this->newOption['is_default']) {
            ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
                ->where('id', '!=', ComponentTypeFieldOption::max('id'))
                ->update(['is_default' => false]);
        }

        $this->reset('newOption');
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción agregada correctamente');
    }

    public function updateOption($optionId, $field, $value)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        
        if ($field === 'is_default' && $value) {
            // Desmarcar otros defaults
            ComponentTypeFieldOption::where('component_type_field_id', $this->fieldId)
                ->where('id', '!=', $optionId)
                ->update(['is_default' => false]);
        }

        $option->update([$field => $value]);
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción actualizada');
    }

    public function deleteOption($optionId)
    {
        $option = ComponentTypeFieldOption::findOrFail($optionId);
        
        if ($option->isInUse()) {
            $this->dispatch('alertFailed', 'No se puede eliminar: la opción está en uso');
            return;
        }

        $option->delete();
        $this->loadField();
        $this->dispatch('alertSuccess', 'Opción eliminada');
    }

    public function reorderOptions($optionIds)
    {
        foreach ($optionIds as $order => $id) {
            ComponentTypeFieldOption::where('id', $id)
                ->update(['display_order' => $order + 1]);
        }
        $this->loadField();
    }

    public function render()
    {
        return view('livewire.component-type-field-options.component-type-field-options');
    }
}

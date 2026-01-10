<?php

namespace App\Livewire\ModalCreateSelect;

use App\Models\Brand;
use App\Models\ComponentFail;
use App\Models\Condition;
use App\Models\EquipmentType;
use App\Models\MechanismFail;
use App\Models\Process;
use App\Models\Ubication;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ModalCreateSelect extends Component
{
    public $label;
    public $field;
    public $updateOptions;
    public $valueNew;
    public $editingId = null;
    public $search = '';

    protected $listeners = ['reset-modal-select'];

    public function resetModalSelect($data)
    {
        if (isset($data['field']) && $data['field'] === $this->field) {
            $this->resetForm();
        }
    }

    // Mapeo de modelos
    private $modelMap = [
        'brand' => Brand::class,
        'equipment_type' => EquipmentType::class,
        'process' => Process::class,
        'ubication' => Ubication::class,
        'condition' => Condition::class,
        'component_failure' => ComponentFail::class,
        'failure_mechanism' => MechanismFail::class,
    ];

    public function mount($label, $field, $updateOptions)
    {
        $this->label = $label;
        $this->field = $field;
        $this->updateOptions = $updateOptions;
    }

    public function getItemsProperty()
    {
        if (!isset($this->modelMap[$this->field])) {
            return collect([]);
        }

        $modelClass = $this->modelMap[$this->field];
        $query = $modelClass::whereNull('deleted_at');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('name', 'asc')->get();
    }

    public function startEdit($id)
    {
        try {
            $item = $this->getModelClass()::find($id);
            if ($item) {
                // Obtener el nombre del item
                $itemName = $item->name;

                // Actualizar las propiedades
                $this->editingId = (int)$id;
                $this->valueNew = (string)$itemName;
                $this->resetValidation();

                // Mantener el modal abierto
                $this->js("
                    if (typeof window.markModalAsOpen === 'function') {
                        window.markModalAsOpen('{$this->field}');
                    }
                ");

                // Disparar un evento personalizado con los datos para que JavaScript lo maneje
                $this->dispatch('edit-item-started', [
                    'field' => $this->field,
                    'id' => $id,
                    'name' => $itemName
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al cargar el registro: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->valueNew = '';
        $this->resetValidation();
    }

    public function deleteItem($id)
    {
        try {
            $item = $this->getModelClass()::find($id);
            if ($item) {
                $item->delete();
                $this->dispatch($this->updateOptions);
                $this->dispatch('alertSuccess', 'Registro eliminado correctamente');
            }
        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function saveField()
    {
        $rules = [];
        $uniqueRule = Rule::unique($this->getTableName(), 'name')->whereNull('deleted_at');

        // Si estamos editando, excluir el registro actual de la validación unique
        if ($this->editingId) {
            $uniqueRule = $uniqueRule->ignore($this->editingId);
        }

        $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', $uniqueRule];

        $this->validate($rules);

        try {
            $modelClass = $this->getModelClass();

            if ($this->editingId) {
                // Editar registro existente
                $item = $modelClass::find($this->editingId);
                if ($item) {
                    $item->update([
                        'name' => ucfirst(trim($this->valueNew))
                    ]);
                    $message = 'Registro actualizado correctamente';
                    $savedId = $item->id;
                }
            } else {
                // Crear nuevo registro
                $newItem = $modelClass::create([
                    'name' => ucfirst(trim($this->valueNew))
                ]);
                $message = 'Registro creado correctamente';
                $savedId = $newItem->id;
            }

            $this->dispatch($this->updateOptions);
            $this->resetForm();
            $this->dispatch('close-modal-select', $this->field);
            $this->dispatch('alertSuccess', $message);

            // Disparar evento con el ID del registro guardado para seleccionarlo automáticamente
            $this->dispatch('item-saved', [
                'field' => $this->field,
                'id' => $savedId
            ]);

        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al guardar: ' . $e->getMessage());
        }
    }

    private function getModelClass()
    {
        return $this->modelMap[$this->field] ?? null;
    }

    private function getTableName()
    {
        $tableMap = [
            'brand' => 'brands',
            'equipment_type' => 'equipment_types',
            'process' => 'processes',
            'ubication' => 'ubications',
            'condition' => 'conditions',
            'component_failure' => 'component_fails',
            'failure_mechanism' => 'mechanism_fails',
        ];
        return $tableMap[$this->field] ?? 'unknown';
    }

    public function resetForm()
    {
        $this->reset(['valueNew', 'editingId', 'search']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.modal-create-select.modal-create-select', [
            'items' => $this->items
        ]);
    }
}

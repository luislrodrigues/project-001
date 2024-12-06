<?php

namespace App\Livewire\ModalCreateSelect;

use App\Models\Brand;
use App\Models\EquipmentType;
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


    public function saveField(){
        $rules = [];
        if($this->field == 'brand'){
            $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', Rule::unique('brands', 'name')->whereNull('deleted_at')];
        }else if($this->field == 'equipment_type'){
            $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', Rule::unique('equipment_types', 'name')->whereNull('deleted_at')];
        }else if($this->field == 'process'){
            $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', Rule::unique('processes', 'name')->whereNull('deleted_at')];
        }else if($this->field == 'ubication'){
            $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', Rule::unique('ubications', 'name')->whereNull('deleted_at')];
        }
        $this->validate($rules);
        try{
            if($this->field == 'brand'){
                Brand::create([
                    'name' => ucfirst($this->valueNew)
                ]);
            }else if($this->field == 'equipment_type'){
                EquipmentType::create([
                    'name' => ucfirst($this->valueNew)
                ]);
            }else if($this->field == 'process'){
                Process::create([
                    'name' => ucfirst($this->valueNew)
                ]);
            }else if($this->field == 'ubication'){
                Ubication::create([
                    'name' => ucfirst($this->valueNew)
                ]);
            }
            $this->dispatch($this->updateOptions);
            $this->resetForm();
            $this->dispatch('close-modal-select',$this->field);
            $this->dispatch('alertSuccess','Se guardo correctamente');
        } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al guardar: ' . $e->getMessage());
        }
    }
    
    public function resetForm(){
        $this->reset(['valueNew']);
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.modal-create-select.modal-create-select');
    }
}

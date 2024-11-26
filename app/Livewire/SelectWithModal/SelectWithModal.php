<?php

namespace App\Livewire\SelectWithModal;

use App\Models\Brand;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SelectWithModal extends Component
{

    public $label;
    public $options = []; 
    public $selectedOption = null;
    public $newOption = '';
    public $field = '';
    public $updateLists = '';
    public $valueNew = null;


    public function mount($label, $options = [],$field,$updateLists)
    {
        $this->label = $label;
        $this->options = $options;
        $this->field = $field;    
        $this->updateLists = $updateLists;    
    }

    protected $listeners = [
        'updateOptions' => 'updateOptions', 
    ];

    public function addOption()
    {
        if (!empty($this->newOption)) {
            $this->options[] = $this->newOption;
            $this->selectedOption = $this->newOption;
            $this->newOption = '';
            $this->dispatchBrowserEvent('close-modal');
        }
    }

    public function handleSelection($value,$variableName)
    {
        $this->dispatch('valueUpdated', $variableName,$value);
    }
    public function updatedList()
    {
        $this->dispatch($this->updateLists);
    }
    public function saveField(){
        $rules = [];
        if($this->field == 'brand'){
            $rules['valueNew'] = ['bail', 'required', 'string', 'max:255', Rule::unique('brands', 'name')->whereNull('deleted_at')];
        }
        $this->validate($rules);
        try{
            if($this->field == 'brand'){
                Brand::create([
                    'name' => $this->valueNew
                ]);
            }
            $this->updatedList();
            $this->resetForm();
            $this->dispatch('close-modal-select');
            $this->dispatch('alertSuccess','Se guardo correctamente');
        } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al guardar: ' . $e->getMessage());
        }
 

    }
    public function resetForm(){
        $this->reset(['valueNew']);
        $this->resetValidation();
    }
    public function updateOptions($newOptions)
    {
        $this->options = $newOptions;
    }
    public function render()
    {
        return view('livewire.select-with-modal.select-with-modal');
    }
}

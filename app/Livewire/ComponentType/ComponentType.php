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
    
    protected $listeners = ['destroy'];
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
            $rules = [
                'name'         => ['bail','required','string','max:255',Rule::unique('component_types')->whereNull('deleted_at')]
            ];
            $this->validate($rules);
            if(count($this->fields) > 0){
                foreach ($this->fields as $index => $field) {
                    $this->validate([
                        "fields.$index.name" => 'required|string|max:255',
                        "fields.$index.type" => 'required|string|max:255',
                    ]);
                }
                try {
                    $type_component = ModelsComponentType::create([
                        'name'               => $this->name,
                    ]);
                    foreach ($this->fields as $field) {
                        $type_component->componentTypeFields()->create([
                            'name' => $field['name'],
                            'type' => $field['type'],
                            'unit' => $field['unit'],
                        ]);
                    }
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
            $rules = [
                'name'         => ['bail','required','string','max:255',Rule::unique('component_types')->ignore($this->IdToUpdate)->whereNull('deleted_at')]
            ];
            $this->validate($rules);
            if(count($this->fields) > 0){
                foreach ($this->fields as $index => $field) {
                    $this->validate([
                        "fields.$index.name" => 'required|string|max:255',
                        "fields.$index.type" => 'required|string|max:255',
                    ]);
                }
                try {
                    $type_component = ModelsComponentType::findOrFail($this->IdToUpdate);

                    $type_component->update([
                        'name'               => $this->name,
                    ]);
                    foreach ($this->fields as $field) {
                        if($field['id']){
                            $type_component->componentTypeFields()->where('id', $field['id'])->update([
                                'name' => $field['name'],
                                'type' => $field['type'],
                                'unit' => $field['unit'],
                            ]);
                        }else{
                            $type_component->componentTypeFields()->create([
                                'name' => $field['name'],
                                'type' => $field['type'],
                                'unit' => $field['unit'],
                            ]);
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
        $this->fields[] = ['name' => '', 'type' => '', 'unit' => ''];
    }


    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function loadEquipment($id){
        $type_component = ModelsComponentType::findOrFail($id);

        if($type_component){
            $this->IdToUpdate = $type_component->id;
            $this->name = $type_component->name;
            $this->fields =  $type_component->componentTypeFields->toArray();
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
        $this->resetValidation();
    }
}

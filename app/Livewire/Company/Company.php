<?php

namespace App\Livewire\Company;

use App\Models\Company as ModelsCompany;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Company extends Component
{
    use WithPagination;
    public  $search, $status,$name,$companyIdToUpdate;
    
    public function render()
    {
       $companies = ModelsCompany::query();
        if (!empty($this->search)) {
           $companies->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
       $companies =$companies->paginate(10); 
        return view('livewire.company.company', [
            'companies'   =>$companies
        ])->layout('layouts.app');
    }

    public function saveCompany()
    {
        $this->validate([
            'name'      => ['bail', 'required', 'string','max:255', Rule::unique('companies')->whereNull('deleted_at')],
        ]);
        try {
            ModelsCompany::create([
                'name'      => $this->name,
            ]);
            $this->resetForm();
            $this->dispatch('close-modal-company');
            $this->dispatch('alertSuccess','Se guardo la empresa correctamente');
     } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al guardar la empresa: ' . $e->getMessage());
        }
    }

    
    public function updateCompany()
    {
        $this->validate([
            'name'      => ['bail', 'required', 'string','max:255', Rule::unique('companies')->ignore($this->companyIdToUpdate)->whereNull('deleted_at')],
        ]);
        try{

            $company = ModelsCompany::findOrFail($this->companyIdToUpdate);

            $company->update([
                'name' => $this->name,
            ]);
            $this->resetForm();
            $this->dispatch('close-modal-company');
            $this->dispatch('alertSuccess', 'Se actualizó la empresa correctamente');
    }catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al actualizar la empresa: ' . $e->getMessage());
        }
    }
    public function resetForm()
    {
        $this->reset(['name', 'companyIdToUpdate']);
        $this->resetValidation();
    }

    public function editCompany($companyId)
    {
        $company = ModelsCompany::findOrFail($companyId);
        $this->name = $company->name;
        $this->companyIdToUpdate = $companyId;
    }
    protected $listeners = ['destroy'];
    public function destroy($id)
    {
        try{
            $company = ModelsCompany::findOrFail($id);
            $company->equipments()->update(['status' => 'inactive']);
            $company->equipments()->delete();
            $company->status = 'inactive';
            $company->save();
            $company->delete();
            $this->dispatch('alertSuccess', 'La empresa se elimino correctamente.');
        }catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al eliminar la  empresa: ' . $e->getMessage());
        }
    }
}

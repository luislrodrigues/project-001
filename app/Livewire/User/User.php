<?php

namespace App\Livewire\User;

use App\Models\Company;
use App\Models\User as ModelsUser;
use GuzzleHttp\Psr7\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class User extends Component

{
    use WithPagination;
    public  $search, $status,$name,$email,$password,$userIdToUpdate,$area,$workstation,$company_id,$company_search,$companies;
    

    
    public function mount()
    {
        $this->companies = Company::whereNull('deleted_at')->where('status','active')->get();
    }
    public function render()
    {
        $users = ModelsUser::query();
        if (!empty($this->status)) {
            $users->where('status', $this->status);
        }
        if (!empty($this->company_search)) {
            $users->where('company_id', $this->company_search);
         }
        if (!empty($this->search)) {
            $users->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        $users = $users->with('company')->paginate(10); 
        return view('livewire.user.user', [
            'users'   => $users
        ])->layout('layouts.app');
    }


    public function saveUser()
    {
        if (auth()->user()->can('save user')) {
            $this->validate([
                'name'       => ['bail', 'required', 'string','max:255'],
                'workstation'=> ['bail', 'required', 'string','max:255'],
                'area'       => ['bail', 'required', 'string','max:255'],
                'company_id' => ['required', 'integer','exists:companies,id'],
                'email'      => ['bail', 'required', 'email', Rule::unique('users')->whereNull('deleted_at')],
                'password'   => ['bail','string', 'nullable', 'min:8'],
            ]);
            try {
               $user = ModelsUser::create([
                    'name'         => $this->name,
                    'email'        => $this->email,
                    'password'     => bcrypt($this->password),
                    'raw_password' => $this->password,
                ]);
                $user->assignRole('user');
                $this->resetForm();
                $this->dispatch('close-modal-user');
                $this->dispatch('alertSuccess','Se guardo el usuario correctamente');
         } catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al guardar el usuario: ' . $e->getMessage());
            }
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }

    

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password','userIdToUpdate','company_id','workstation','area']);
        $this->resetValidation();
    }

    public function editUser($userId)
    {
        $user = ModelsUser::findOrFail($userId);
        $this->name = $user->name;
        $this->area = $user->area;
        $this->workstation = $user->workstation;
        $this->company_id = $user->company_id;
        $this->email = $user->email;
        $this->password = $user->raw_password; 
        $this->userIdToUpdate = $userId;
        $this->dispatch('open-modal-edit-user');
    }

    public function updateUser()
    {
        if (auth()->user()->can('edit user')) {
            $this->validate([
                'name'       => ['bail', 'required', 'string','max:255'],
                'workstation'=> ['bail', 'required', 'string','max:255'],
                'company_id' => ['required', 'integer','exists:companies,id'],
                'area'       => ['bail', 'required', 'string','max:255'],
                'email'      => ['bail', 'required', 'email', Rule::unique('users')->ignore($this->userIdToUpdate)->whereNull('deleted_at')],
                'password'   => ['bail','string', 'nullable', 'min:8'],
            ]);
            try{
    
                $user = ModelsUser::findOrFail($this->userIdToUpdate);
                $dataToUpdate = [
                    'name'         => $this->name,
                    'email'        => $this->email,
                    'company_id'   => $this->company_id,
                    'workstation'  => $this->workstation,
                    'area'         => $this->area,
                    'email'        => $this->email,
                    'raw_password' => $this->password,
                ];
                if (!empty($this->password)) {
                    $dataToUpdate['password'] = bcrypt($this->password);
                }
                $user->update($dataToUpdate);
                $this->resetForm();
                $this->dispatch('close-modal-user');
                $this->dispatch('alertSuccess', 'Se actualizó el usuario correctamente');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al actualizar el usuario: ' . $e->getMessage());
            }
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }


    public function toggleUserStatus($userId)
    {
        if (auth()->user()->can('inactive user')) {
            try{
                $user = ModelsUser::findOrFail($userId);
                $user->status = ($user->status == 'active') ? 'inactive' : 'active';
                $user->save();
                $this->dispatch('alertSuccess', 'Estado del usuario actualizado correctamente.');
            }catch (\Exception $e) {
                $this->dispatch('alertFailed', 'Error al actualizar el estado del  usuario: ' . $e->getMessage());
            }
        } else {
            abort(403, 'No tienes los permisos requeridos.');
        }
    }
}

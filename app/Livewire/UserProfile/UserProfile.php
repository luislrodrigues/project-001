<?php

namespace App\Livewire\UserProfile;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserProfile extends Component
{
    public $name;
    public $email;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $workstation;
    public $company_id;
    public $companies;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->workstation = $user->workstation ?? '';
        $this->company_id = $user->company_id;
        $this->companies = Company::whereNull('deleted_at')->where('status', 'active')->get();
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
            'workstation' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ]);

        try {
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'workstation' => $this->workstation,
                'company_id' => $this->company_id,
            ]);

            $this->dispatch('alertSuccess', 'Perfil actualizado correctamente');
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'La contraseña actual es incorrecta');
            return;
        }

        try {
            $user->update([
                'password' => Hash::make($this->password),
            ]);

            $this->current_password = '';
            $this->password = '';
            $this->password_confirmation = '';
            $this->resetValidation();
            $this->dispatch('alertSuccess', 'Contraseña actualizada correctamente');
        } catch (\Exception $e) {
            $this->dispatch('alertFailed', 'Error al actualizar la contraseña: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-profile.user-profile')->layout('layouts.app');
    }
}

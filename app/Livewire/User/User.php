<?php

namespace App\Livewire\User;

use App\Models\User as ModelsUser;
use Livewire\Component;
use Livewire\WithPagination;

class User extends Component

{
    use WithPagination;
    public $user_name, $email, $password, $role_id, $status, $id_selected, $search, $roles;
    public function render()
    {

        $users = ModelsUser::paginate(7);

        return view('livewire.user.user', compact('users'))->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Company;

use Livewire\Component;

class Company extends Component
{
    public function render()
    {
        return view('livewire.company.company')->layout('layouts.app');
    }
}

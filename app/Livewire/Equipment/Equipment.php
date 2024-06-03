<?php

namespace App\Livewire\Equipment;

use Livewire\Component;

class Equipment extends Component
{
    public function render()
    {
        return view('livewire.equipment.equipment')->layout('layouts.app');
    }
}

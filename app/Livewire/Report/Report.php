<?php

namespace App\Livewire\Report;

use Livewire\Component;

class Report extends Component
{

    public $title;

    public function mount()
    {
        $this->title = 'Título inicial';
    }

    public function updateTitle()
    {
        $this->title = 'Nuevo título';
    }
    public function render()
    {
        return view('livewire.report.report')->layout('layouts.app');
    }
}

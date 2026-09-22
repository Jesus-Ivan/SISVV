<?php

namespace App\Livewire\Lockers;

use Livewire\Component;

class EditarCasillero extends Component
{
    public $idLocker;


    public function render()
    {
        return view('livewire.lockers.editar-casillero');
    }
}

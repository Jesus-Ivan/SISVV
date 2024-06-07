<?php

namespace App\Livewire\Puntos\Ventas;

use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    
    public $codigopv;
    public $search;
    public $fecha;

    public function mount(){
        $this->fecha = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.puntos.ventas.principal');
    }
}

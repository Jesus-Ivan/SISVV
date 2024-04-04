<?php

namespace App\Livewire\Recepcion;

use Livewire\Component;

class VentasReporte extends Component
{
    public $codigopv;

    public function mount(string $codigopv){
        $this->codigopv = $codigopv;;
    }

    public function mostrar(){
        dd($this->codigopv);
    }

    public function render()
    {
        return view('livewire.recepcion.ventas-reporte');
    }
}

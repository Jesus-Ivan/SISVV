<?php

namespace App\Livewire\Almacen\Produccion;

use App\Models\Bodega;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NuevaProduccion extends Component
{
    #[Computed()]
    public function bodegas(){
        return Bodega::all();
    }
    public function render()
    {
        return view('livewire.almacen.produccion.nueva-produccion');
    }
}

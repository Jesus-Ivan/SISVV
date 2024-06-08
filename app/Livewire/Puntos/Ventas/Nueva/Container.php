<?php

namespace App\Livewire\Puntos\Ventas\Nueva;

use App\Livewire\Forms\VentaForm;
use App\Models\Socio;
use Livewire\Attributes\On;
use Livewire\Component;

class Container extends Component
{
    public VentaForm $ventaForm;

    #[On('on-selected-socio')]
    public function socioSeleccionado(Socio $socio){
        $this->ventaForm->socioSeleccionado = $socio;
    }

    public function render()
    {
        return view('livewire.puntos.ventas.nueva.container');
    }
}

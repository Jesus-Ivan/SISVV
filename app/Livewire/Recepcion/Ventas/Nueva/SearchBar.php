<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchBar extends Component
{
    #[Modelable]
    public $socioSeleccionado;

    #[On('on-selected-socio')]
    public function onSelectedInput(Socio $socioId)
    {
        $this->socioSeleccionado = $socioId->toArray();
    }
    

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.search-bar');
    }
}

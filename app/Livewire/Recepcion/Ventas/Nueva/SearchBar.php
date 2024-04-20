<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchBar extends Component
{
    public Socio $socioSeleccionado;

    #[On('on-selected-result')]
    public function onSelectedInput(Socio $socioId)
    {
        $this->socioSeleccionado = $socioId;
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.search-bar');
    }
}

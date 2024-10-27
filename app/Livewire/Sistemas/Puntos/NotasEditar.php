<?php

namespace App\Livewire\Sistemas\Puntos;

use Livewire\Attributes\Locked;
use Livewire\Component;

class NotasEditar extends Component
{
    #[Locked]
    public $folio;

    //Setear el valor obtenido desde del controlador
    public function mount($folio){
        $this->folio = $folio;
    }

    public function render()
    {
        return view('livewire.sistemas.puntos.notas-editar');
    }
}

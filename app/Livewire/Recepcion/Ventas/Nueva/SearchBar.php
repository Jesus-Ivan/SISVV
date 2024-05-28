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

    public $invitado = false;

    #[On('on-selected-socio')]
    public function onSelectedInput(Socio $socioId)
    {
        $this->socioSeleccionado = $socioId->toArray();
    }

    //hook, que se ejecuta despues de actualizar la propiedad invitado desde el front
    public function updatedInvitado()
    {
        //Si desactiva el switch, borrar los datos del INVITADO
        if (!$this->invitado) {
            $this->socioSeleccionado = [];
            //emitir evento
            $this->dispatch('on-invitado', false, $this->socioSeleccionado);
        } else {
            //Asignar socio invitado para la venta
            $this->socioSeleccionado = [
                'nombre' => 'INVITADO',
                'apellido_p' => '',
                'apellido_m' => ''
            ];
            //emitir evento
            $this->dispatch('on-invitado', true, $this->socioSeleccionado);
        }
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.search-bar');
    }
}

<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use App\Models\SocioMembresia;
use Exception;
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
        try {
            //Validamos si el socio no esta con una membresia cancelada
            $resultMembresia = SocioMembresia::where('id_socio', $socioId->id)->first();
            if (!$resultMembresia) {
                throw new Exception("No se encontro membresia registrada");
            } else if ($resultMembresia->estado == 'CAN') {
                throw new Exception("Membresia de socio $socioId->id cancelada");
            }
            $this->socioSeleccionado = $socioId->toArray();
        } catch (\Throwable $th) {
            session()->flash('fail_socio',  $th->getMessage());
        }
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

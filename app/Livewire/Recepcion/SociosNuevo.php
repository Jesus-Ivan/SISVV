<?php

namespace App\Livewire\Recepcion;

use App\Livewire\Forms\NuevoSocio;
use Livewire\Component;
use Livewire\WithFileUploads;

class SociosNuevo extends Component
{
    use WithFileUploads;

    public NuevoSocio $formSocio;

    public function register()
    {
        //Intentamos registrar al socio con el objeto del form
        if ($this->formSocio->store()) {
            //Enviamos flash message, al action-message
            session()->flash('success', "Socio registrado con exito");
        } else {
            //Enviamos flash message, al action-message (error)
            session()->flash('fail', 'Ocurrio un error');
        }
        //emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    public function agregarMiembro()
    {
        $this->formSocio->crearMiembro();
    }
    public function borrarMiembro($temp)
    {
        $this->formSocio->quitarMiembro($temp);
    }

    public function render()
    {
        return view('livewire.recepcion.socios-nuevo');
    }
}

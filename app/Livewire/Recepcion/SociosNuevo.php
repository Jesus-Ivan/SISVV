<?php

namespace App\Livewire\Recepcion;

use App\Livewire\Forms\SocioForm;
use App\Models\Membresias;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class SociosNuevo extends Component
{
    use WithFileUploads;

    public SocioForm $formSocio;

    #[Computed()]
    public function membresias()
    {
        return Membresias::all();
    }

    //Se comprueba el tipo de membresia, para restringir el registro de integrantes
    public function comprobarMembresia($value)
    {
        $this->formSocio->comprobar($value);
    }

    public function register()
    {
        //Intentamos registrar al socio con el objeto del form
        try {
            $this->formSocio->store();
            //Enviamos flash message, al action-message
            session()->flash('success', "Socio registrado con exito");
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
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

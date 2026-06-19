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
        return Membresias::where('disponible', true)->get();
    }

    //Se ejecuta cada vez que cambian las membresias seleccionadas, para restringir el registro de integrantes
    public function comprobarMembresias(): void
    {
        $this->formSocio->comprobarMultiples();
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
        try {
            $this->formSocio->crearMiembro();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('open-action-message');
        }
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

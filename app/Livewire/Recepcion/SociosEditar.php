<?php

namespace App\Livewire\Recepcion;

use App\Livewire\Forms\SocioForm;
use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class SociosEditar extends Component
{
    use WithFileUploads;
    public SocioForm $form;

    #[Computed()]
    public function membresias()
    {
        return Membresias::all();
    }

    //Inicializar el componente con los valores, obtenidos desde el controlador
    public function mount($socio)
    {
        $this->form->setSocio($socio);
        $this->form->setIntegrantes($socio);
    }

    public function agregarMiembro()
    {
        $this->form->crearMiembro();
    }

    public function borrarMiembro($temp)
    {
        $this->form->quitarMiembro($temp);
    }

    public function editarMiembro($integrante)
    {
        $this->form->editMiembro($integrante);
    }
    public function cancelarEdicion()
    {
        $this->form->cleanEdit();
    }
    public function confirmarEdicion($index_interante_BD)
    {
        try {
            $this->form->confirmEdit($index_interante_BD);
            session()->flash('success', "Integrante actualizado correctamente");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }
    public function confirmarEliminacion()
    {
        try {
            $this->form->confirmDelete();
            session()->flash('success', "Integrante eliminado correctamente");
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
        //Emitir evento para cerrar el modal
        $this->dispatch('close-modal');
    }
    public function saveSocio()
    {
        //Intentamos guardar cambios al socio con el objeto del form
        try {
            $this->form->update();
            //Enviamos flash message, al action-message
            session()->flash('success', "Socio actualizado con exito");
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

    public function registrarIntegrante()
    {
        try {
            $this->form->registerIntegrante();
            session()->flash('success', "Integrante registrado correctamente");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    public function eliminarIntegrante($integrante)
    {
        $this->form->selectMiembro($integrante);
        $this->dispatch('open-modal',  name: 'modalEliminar'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
    }

    public function render()
    {
        return view('livewire.recepcion.socios-editar');
    }
}

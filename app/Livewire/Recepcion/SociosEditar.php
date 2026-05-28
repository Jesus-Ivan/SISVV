<?php

namespace App\Livewire\Recepcion;

use App\Livewire\Forms\SocioForm;
use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\SocioMembresia;
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
        // Disponibles + las que el socio ya tiene asignadas (aunque disponible=false)
        return Membresias::where('disponible', true)
            ->orWhereIn('clave', $this->form->claves_membresia)
            ->get();
    }

    //Inicializar el componente con los valores, obtenidos desde el controlador
    public function mount($socio)
    {
        $this->form->setSocio($socio);
        $this->form->setIntegrantes($socio);
    }

    //Se ejecuta cada vez que cambian las membresias seleccionadas, para restringir el registro de integrantes
    public function comprobarMembresias(): void
    {
        $this->form->comprobarMultiples();
    }

    //Creamos un miembro de la membresia de forma temporal
    public function agregarMiembro()
    {
        $this->form->crearMiembro();
    }

    //Elimina al miembro en funcion del atributo temporal
    public function borrarMiembro($temp)
    {
        $this->form->quitarMiembro($temp);
    }

    //Establecer en el formulario, el miembro a editar
    public function editarMiembro($integrante)
    {
        $this->form->editMiembro($integrante);
    }

    //Salir del modo de edicion del miembro
    public function cancelarEdicion()
    {
        $this->form->cleanEdit();
    }

    //Guardar la edicion de los cambios del integrante del socio
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

    //Se ejecuta desde el front, para guardar cambios del socio
    public function saveSocio()
    {
        //Si no hay membresias seleccionadas, delegamos a actualizarSocio para que
        //la validacion del formulario muestre el error correspondiente
        if (empty($this->form->claves_membresia)) {
            $this->actualizarSocio();
            return;
        }

        //Si la nueva seleccion deja al socio solo con membresias INDIVIDUAL y existen integrantes,
        //pedimos confirmacion porque la actualizacion eliminara a los integrantes
        if ($this->revisarPerdidaIntegrantes()) {
            $this->dispatch('open-modal',  name: 'modalAdvertencia');
        } else {
            $this->actualizarSocio();
        }
    }

    public function confirmarActualizacion(){
        //Intentamos guardar cambios al socio con el objeto del form
        try {
            $this->form->confirmUpdate();
            //Enviamos flash message, al action-message
            session()->flash('success', "Socio actualizado con exito");
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para cerrar el modal
        $this->dispatch('close-modal');
        //emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    private function actualizarSocio()
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
    //Determina si al guardar se perderan los integrantes (todas las membresias activas quedarian INDIVIDUAL)
    private function revisarPerdidaIntegrantes(): bool
    {
        $totalIntegrantes = \App\Models\IntegrantesSocio::where('id_socio', $this->form->socio->id)->count();
        if ($totalIntegrantes === 0) {
            return false;
        }

        //Las membresías canceladas se eliminarán, no cuentan para decidir si quedan integrantes
        foreach ($this->form->claves_membresia as $clave) {
            $estado = $this->form->estados_membresia[$clave] ?? 'MEN';
            if ($estado === 'CAN') {
                continue;
            }
            $membresia = Membresias::find($clave);
            if ($membresia && strpos($membresia->descripcion, 'INDIVIDUAL') === false) {
                return false;
            }
        }

        //Todas las activas son INDIVIDUAL (o no quedan activas): se perderian los integrantes
        return true;
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

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
        return Membresias::all();
    }

    //Inicializar el componente con los valores, obtenidos desde el controlador
    public function mount($socio)
    {
        $this->form->setSocio($socio);
        $this->form->setIntegrantes($socio);
    }

    /*Se comprueba el tipo de membresia, para restringir el registro de integrantes,
      gracias al evento change, desde el front
    */
    public function comprobarMembresia($value)
    {
        $this->form->comprobar($value);
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
        if ($this->revisarCambioMembresia()) {
            //ABRIMOS EL MODAL PARA PODER confirmar, el cambio a INDIVIDUAL
            $this->dispatch('open-modal',  name: 'modalAdvertencia');
        } else {
            //continuar la actualizacion de forma normal
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
    //Determina el cambio de membresia, antes de confimar el guardado.
    private function revisarCambioMembresia()
    {
        //Buscamos de la tabla 'socios_membresias', la clave de membresia, asociada al id, del socio
        $socioMembresia = SocioMembresia::where('id_socio', $this->form->socio->id)->get()[0];
        //Buscamos de la tabla 'membresias', los detalles de la membresia registrada en la BD
        $membresiaOriginal = Membresias::find($socioMembresia->clave_membresia);
        //Buscamos de la tabla 'membresias', los detalles de la membresia modificada en el form
        $membresiaModificada = Membresias::find($this->form->clave_membresia);

        if (!strpos($membresiaOriginal->descripcion, "INDIVIDUAL") && strpos($membresiaModificada->descripcion, "INDIVIDUAL")) {
            return true;
        }
        return false;
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

<?php

namespace App\Livewire\Almacen\Grupos;

use App\Constants\AlmacenConstants;
use App\Models\Grupos;
use App\Models\Subgrupos;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Productos extends Component
{
    #[Validate('required|min:2|max:200')]
    public $descripcionProd;

    #[Validate('required')]
    public $clasificacionProd;

    public $selectedProductoId = null;

    public $descripcion_subgrupo = '';
    public $subgrupos = [];

    //Propiedades para eliminar un grupo
    public $eliminarGrupoId = null;
    public $eliminarGrupoDescripcion = '';
    public $eliminarSubgrupos = false;

    //Propiedad para el comportamiento de la página
    public $isFormActive = false;

    /**
     * Reseteamos los campos de informacion
     */
    public function limpiarCampos()
    {
        $this->reset([
            'descripcionProd',
            'clasificacionProd',
            'selectedProductoId',
            'descripcion_subgrupo',
            'subgrupos',
            'eliminarGrupoId',
            'eliminarGrupoDescripcion',
            'eliminarSubgrupos',
        ]);
        $this->isFormActive = false; // Desactivar el formulario
        $this->resetErrorBag();
    }

    /**
     * Activa los campos para llenar
     */
    public function activarFormulario()
    {
        $this->limpiarCampos();
        $this->isFormActive = true;
    }
    
    /**
     * Creamos un nuevo registro o editamos uno existente
     */
    public function register()
    {
        try {
            $validated = $this->validate();
            if ($this->selectedProductoId) {
                $this->actualizarGrupo($validated);
            } else {
                $this->crearGrupo($validated);
            }
            $this->limpiarCampos();
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    /**
     * Creamos un nuevo Grupo y Subgrupos
     */
    private function crearGrupo(array $validated)
    {
        $grupo = Grupos::create([
            'descripcion' => $validated['descripcionProd'],
            'tipo' => AlmacenConstants::PRODUCTOS_KEY,
            'clasificacion' => $validated['clasificacionProd'],
        ]);
        //Creamos los subgrupos correspondientes
        foreach ($this->subgrupos as $subgrupo) {
            Subgrupos::create([
                'id_grupo' => $grupo->id,
                'descripcion' => $subgrupo,
            ]);
        }
        session()->flash('success', 'Producto agregado correctamente.');
    }

    /**
     * Actualizamos un grupo existente
     */
    private function actualizarGrupo()
    {
        $grupo = Grupos::find($this->selectedProductoId);
        if ($grupo) {
            $grupo->descripcion = $this->descripcionProd;
            $grupo->clasificacion = $this->clasificacionProd;
            $grupo->save();

            $grupo->subgrupos()->delete(); // Elimina los subgrupos actuales
            foreach ($this->subgrupos as $subgrupo) {
                Subgrupos::create([
                    'id_grupo' => $grupo->id,
                    'descripcion' => $subgrupo,
                ]);
            }
            session()->flash('success', 'Producto actualizado correctamente.');
        }
    }

    /**
     * Crear subgrupo a la lista
     */
    public function crearSubgrupo()
    {
        $this->validate(['descripcion_subgrupo' => 'required|min:3|max:250']);
        $this->subgrupos[] = $this->descripcion_subgrupo;
        // Limpiar el campo de entrada
        $this->reset('descripcion_subgrupo');
    }

    /**
     * Carga los datos de un producto para edición, incluyendo sus subgrupos.
     */
    public function editProducto($id)
    {
        $grupo = Grupos::find($id);
        if ($grupo) {
            $this->selectedProductoId = $grupo->id;
            $this->descripcionProd = $grupo->descripcion;
            $this->clasificacionProd = $grupo->clasificacion;
            $this->subgrupos = $grupo->subgrupos->pluck('descripcion')->toArray();
            $this->isFormActive = true;
        } else {
            //$this->resetForm(); // Limpiar si el producto no se encuentra
            $this->limpiarCampos();
            session()->flash('fail', 'Producto no encontrado para editar.');
            $this->dispatch('open-action-message');
        }
    }

    /**
     * Funcion para mostrar los subgrupos en la vista
     */
    public function selectGrupo($id)
    {
        $this->selectedProductoId = $id;
        $this->editProducto($id);
    }

    /**
     * Cancelamos la edicion de un grupo
     */
    public function cancelarEdit()
    {
        $this->limpiarCampos();
    }

    /**
     * Modal para eliminar un grupo de la lista
     */
    public function deleteGrupo($id)
    {
        $grupo = Grupos::find($id);
        if ($grupo) {
            $this->eliminarGrupoId = $grupo->id;
            $this->eliminarGrupoDescripcion = $grupo->descripcion;
            $this->eliminarSubgrupos = $grupo->subgrupos->isNotEmpty();
            $this->dispatch('open-modal', name: 'modalEliminar');
        } else {
            session()->flash('fail', 'Grupo no encontrado para eliminar.');
            $this->dispatch('open-action-message');
        }
    }

    /**
     * Elimina un grupo de la lista
     */
    public function eliminarGrupo()
    {
        if ($this->eliminarGrupoId) {
            $grupo = Grupos::find($this->eliminarGrupoId);
            if ($grupo) {
                $grupo->delete();
                session()->flash('success', 'Grupo eliminado correctamente.');
            } else {
                session()->flash('fail', 'Grupo no encontrado para eliminar.');
            }
        } else {
            session()->flash('fail', 'No se ha seleccionado ningún grupo para eliminar.');
        }
        $this->dispatch('close-modal', name: 'modalEliminar'); // Cerramos el modal
        $this->dispatch('open-action-message');
        $this->limpiarCampos();
    }

    /**
     * Elimina un subgrupo de la lista temporal
     */
    public function eliminarSubgrupo($index)
    {
        unset($this->subgrupos[$index]);
        $this->subgrupos = array_values($this->subgrupos);
    }

    public function render()
    {
        $grupos = Grupos::where('tipo', '!=', 'INSUM')->get();

        return view(
            'livewire.almacen.grupos.productos',
            [
                'clasificaciones' => [
                    AlmacenConstants::ALIMENTOS_KEY,
                    AlmacenConstants::BEBIDAS_KEY
                ],
                'listaGrupos' => $grupos
            ]
        );
    }
}

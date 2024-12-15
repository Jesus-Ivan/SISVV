<?php

namespace App\Livewire\Almacen\Articulos;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\ArticulosForm;
use App\Models\Clasificacion;
use App\Models\Departamentos;
use App\Models\Proveedor;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ArticuloEditar extends Component
{
    public ArticulosForm $formEdit;

    #[Computed()]
    public function clasificacion()
    {
        return Clasificacion::all();
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function unidades()
    {
        return Unidad::all();
    }

    #[Computed()]
    public function departamentos()
    {
        return Departamentos::all();
    }

    //Iniciamos con los componentes desde el controlador
    public function mount($articulo)
    {
        $this->formEdit->setArticulo($articulo);
        $this->formEdit->setUnidades($articulo);
    }

    public function añadirUnidad()
    {
        $this->formEdit->crearUnidad();
    }

    public function confirmarEliminar($index_unidad)
    {
        $this->formEdit->eliminarUnidad($index_unidad);
    }

    public function confirmEdit()
    {
        try {
            $result =  $this->formEdit->validar();
            DB::transaction(function () use ($result) {
                //Guardamos los cambios en el articulo
                $this->formEdit->guardarInfoGeneral($result);
                //Guardamos los cambios en las unidades
                $this->formEdit->guardarInfoUnidades();
            });
            session()->flash('success', 'Articulo actualizado correctamente');
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

    public function render()
    {
        return view('livewire.almacen.articulos.articulo-editar',[
            'tipos' => [
                AlmacenConstants::ABARROTES_KEY => 'ABARROTES',
                AlmacenConstants::MATERIA_KEY => 'MATERIA PRIMA',
                AlmacenConstants::SEMIPORDUCIDO_KEY => 'SEMIPRODUCIDO',
                AlmacenConstants::SERVICIO_KEY => 'SERVICIO',
                AlmacenConstants::PLATILLOS_KEY => 'PLATILLOS',
                AlmacenConstants::BEBIDAS_KEY => 'BEBIDAS'
            ]
        ]);
    }
}

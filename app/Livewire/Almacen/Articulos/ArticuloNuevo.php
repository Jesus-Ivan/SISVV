<?php

namespace App\Livewire\Almacen\Articulos;

use App\Livewire\Forms\ArticulosForm;
use App\Models\Clasificacion;
use App\Models\Proveedor;
use App\Models\Unidad;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ArticuloNuevo extends Component
{
    public ArticulosForm $formArticulo;

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

    public function register()
    {
        try {
            $this->formArticulo->articuloNuevo();
            session()->flash('success', 'Articulo registrado con exito');
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

    public function agregarUnidad()
    {
        $this->formArticulo->crearUnidad();
    }

    public function borrarUnidad($temp)
    {
        $this->formArticulo->quitarUnidad($temp);    
    }

    public function render()
    {
        return view('livewire.almacen.articulos.articulo-nuevo');
    }
}

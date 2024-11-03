<?php

namespace App\Livewire\Almacen\Articulos;

use App\Livewire\Forms\ArticulosForm;
use App\Models\Clasificacion;
use App\Models\Proveedor;
use App\Models\Unidad;
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

    //Iniciamos con los componentes desde el controlador
    public function mount($articulo)
    {
        $this->formEdit->setArticulo($articulo);
    }


    public function render()
    {
        return view('livewire.almacen.articulos.articulo-editar');
    }
}

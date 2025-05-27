<?php

namespace App\Livewire\Almacen\Presentaciones;

use App\Models\Presentacion;
use App\Models\Proveedor;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    public $search_input = "", $grupo, $proveedor = "", $estado = "";

    public function search()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function presentaciones()
    {
        $result = Presentacion::with('proveedor')
            ->where('descripcion', 'like', "%$this->search_input%");

        //Si se selecciono un proveedor ($proveedor es cualquier string "1", "9", diferente de "")
        if (strlen($this->proveedor) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('id_proveedor', '=', $this->proveedor);
        }

        //Si estado $estado es diferente de ""
        if (strlen($this->estado) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('estado', '=', $this->estado);
        }
        return $result->paginate(20);
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    public function render()
    {
        return view('livewire.almacen.presentaciones.principal');
    }
}

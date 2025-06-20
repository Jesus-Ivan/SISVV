<?php

namespace App\Livewire\Almacen\Productos;

use App\Constants\AlmacenConstants;
use App\Models\Grupos;
use App\Models\Producto;
use App\Models\Subgrupos;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search_input = "", $grupo = "", $estado = "";

    public function search()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function productos()
    {
        $result = Producto::with(['grupo', 'subgrupo'])
            ->whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%");

        //Si  $grupo es diferente de ""
        if (strlen($this->grupo) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('id_grupo', '=', $this->grupo);
        }

        //Si estado $estado es diferente de ""
        if (strlen($this->estado) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('estado', '=', $this->estado);
        }
        return $result->paginate(20);
    }

    public function actualizarSubGrupo() {}

    #[Computed()]
    public function grupos()
    {
        $result = Grupos::with('subgrupos')
            ->where('tipo', AlmacenConstants::PRODUCTOS_KEY)
            ->get();
        return $result;
    }
    public function render()
    {
        return view('livewire.almacen.productos.principal');
    }
}

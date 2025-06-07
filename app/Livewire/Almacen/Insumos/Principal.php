<?php

namespace App\Livewire\Almacen\Insumos;

use App\Constants\AlmacenConstants;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    public $search_input = '', $grupo = '', $tipo_insumo = '';

    #[Computed()]
    public function insumos()
    {
        $result = Insumo::with(['unidad','grupo'])
            ->whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%");

        //Si $grupo es diferente de ""
        if (strlen($this->grupo) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('id_grupo', '=', $this->grupo);
        }

        //Si $tipo_insumo es diferente de ""
        if (strlen($this->tipo_insumo) > 0) {
            //Anexar el criterio de busqueda a la consulta
            $result->where('elaborado', '=', $this->tipo_insumo);
        }
        return $result->paginate(20);
    }

    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        return $result;
    }

    public function search()
    {
        $this->resetPage();
    }


    public function render()
    {
        return view('livewire.almacen.insumos.principal');
    }
}

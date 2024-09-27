<?php

namespace App\Livewire\Almacen\Ordenes;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Historial extends Component
{
    use WithPagination;

    public $input_search;

    #[Computed()]
    public function productos()
    {
        return DB::table('detalles_compras')
            ->whereAny(['codigo_producto', 'nombre'], 'like', '%' . $this->input_search . '%')
            ->paginate(20);
    }

    public function buscar()
    {
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.almacen.ordenes.historial');
    }
}

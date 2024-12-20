<?php

namespace App\Livewire\Almacen\Existencias;

use App\Models\CatalogoVistaVerde;
use App\Models\Stock;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search_input;

    public function search()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function articulos()
    {
        $result = CatalogoVistaVerde::with('stocks')
        ->whereAny(['codigo', 'nombre'], 'like', '%' . $this->search_input . '%')
        ->whereNot('clave_dpto', 'RECEP')
        ->paginate(20);
        
        return $result;
    }  

    public function render()
    {
        return view('livewire.almacen.existencias.principal');
    }
}

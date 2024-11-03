<?php

namespace App\Livewire\Almacen\Articulos;

use App\Livewire\Forms\ArticulosForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Clasificacion;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component; 

class Principal extends Component
{
    use WithPagination;
    public ArticulosForm $form;
    public $search_input;

    public function search()
    {
        $this->resetPage();
    }

    public function editarArticulo($articulo)
    {
        $this->form->editArticulo($articulo);
    }

    #[Computed()]
    public function articulos()
    {
        return CatalogoVistaVerde::with('proveedor', 'familia', 'categoria')->where('nombre', 'like', '%' . $this->search_input . '%')->orWhere('codigo', '=', $this->search_input)
            ->orderByRaw('catalogo_vista_verde.codigo')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.almacen.articulos.principal');
    }
}

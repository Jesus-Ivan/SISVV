<?php

namespace App\Livewire\Almacen\Inventario;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $mes, $clave_bodega = '';
    public $folio_seleccionado;

    //Hook de inicio del vida del componente
    public function mount()
    {
        $this->mes = substr(now()->toDateString(), 0, 7);
    }

    //Cada vez que busca el usuario, resetar paginador
    public function buscar()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function inventarios()
    {
        return Inventario::with('bodega')
            ->whereMonth('fecha_existencias', substr($this->mes, 5, 2))
            ->whereYear('fecha_existencias', substr($this->mes, 0, 4))
            ->where('clave_bodega', 'like', "%$this->clave_bodega%")
            ->paginate(20);
    }

    //Ver los detalles de un ajuste de inventario
    #[Computed()]
    public function inventario_detalles()
    {
        return DB::table('detalles_inventario')
            ->where('folio_inventario', $this->folio_seleccionado)
            ->get();
    }

    //Utilizado para la tabla de la vista
    public function showDetails($folio)
    {
        //Guardar el folio que selecciono para ver detalles
        $this->folio_seleccionado = $folio;
        //Abrir modal
        $this->dispatch('open-modal', name: 'modal-detalles');
    }


    public function render()
    {
        return view('livewire.almacen.inventario.principal');
    }
}

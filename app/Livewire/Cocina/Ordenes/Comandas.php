<?php

namespace App\Livewire\Cocina\Ordenes;

use App\Constants\PuntosConstants;
use App\Events\ComandaLista;
use App\Models\DetallesVentaProducto;
use App\Models\ZonaImpresion;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Comandas extends Component
{
    use WithPagination;
    public $fecha_busqueda = '';
    public $id_zona = '';

    //Hook al inicio del vida del componente
    public function mount()
    {
        $this->fecha_busqueda = now()->format('Y-m-d');
    }

    #[Computed()]
    public function zonas()
    {
        return ZonaImpresion::all();
    }


    public function render()
    {
        return view('livewire.cocina.ordenes.comandas');
    }
}

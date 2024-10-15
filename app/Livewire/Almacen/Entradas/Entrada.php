<?php

namespace App\Livewire\Almacen\Entradas;

use App\Models\Entrada as ModelsEntrada;
use App\Models\OrdenCompra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Entrada extends Component
{
    use WithPagination;
    public $mes_busqueda;

    public function mount()
    {
        $this->mes_busqueda = now()->toDateString();
    }
    #[Computed()]
    public function entradas()
    {
        //Crear instancia de carbon del mes buscado
        $mes_busqueda = Carbon::parse($this->mes_busqueda);
        //Buscar las entradas
        return ModelsEntrada::whereYear('created_at', $mes_busqueda->year)
            ->whereMonth('created_at', $mes_busqueda->month)
            ->paginate(10);
    }

    /**
     * Se ejecuta para buscar las ordenes.
     */
    public function buscar()
    {
        //Reinicia el paginador.
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.almacen.entradas.entrada');
    }
}

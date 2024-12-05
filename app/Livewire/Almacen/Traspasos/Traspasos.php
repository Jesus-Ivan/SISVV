<?php

namespace App\Livewire\Almacen\Traspasos;

use App\Models\Bodega;
use App\Models\Traspaso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Traspasos extends Component
{
    use WithPagination;

    //Mes de busqueda de la vista, el folio del articulo para ver detalles
    public $mes_busqueda, $traspaso_seleccionado;

    //hook que se ejecuta al inicio del ciclo del vida del componente
    public function mount()
    {
        $this->mes_busqueda = Carbon::now()->toDateString();
    }

    #[Computed()]
    public function traspasos()
    {
        return Traspaso::with('user')
            ->whereYear('created_at', Carbon::parse($this->mes_busqueda)->year)
            ->whereMonth('created_at', Carbon::parse($this->mes_busqueda)->month)
            ->orderBy('folio', 'desc')
            ->paginate(20);
    }

    //Ver los detalles de un traspaso
    #[Computed()]
    public function traspaso_detalles()
    {
        return DB::table('detalles_traspasos')
            ->where('folio_traspaso', $this->traspaso_seleccionado)
            ->get();
    }

    //Utilizado para la tabla de la vista
    public function showDetails($folio)
    {
        //Guardar el folio que selecciono para ver detalles
        $this->traspaso_seleccionado = $folio;
        //Abrir modal
        $this->dispatch('open-modal', name: 'modal-traspaso');
    }

    
    public function buscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $bodegas = Bodega::all();
        return view('livewire.almacen.traspasos.traspasos', ['bodegas' => $bodegas]);
    }
}

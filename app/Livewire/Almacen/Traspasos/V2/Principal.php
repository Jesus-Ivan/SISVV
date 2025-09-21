<?php

namespace App\Livewire\Almacen\Traspasos\V2;

use App\Models\DetalleTraspasoNew;
use App\Models\TraspasoNew;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search_mes;

    //Detalles de traspaso
    public $traspaso_seleccionado, $traspaso_detalles = [];

    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    //Ejecutamos al momento de buscar un traspaso
    public function buscar()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function traspasos()
    {
        //Preparar el constructor de consultas
        $query = TraspasoNew::query();
        //Verificamos si hay mes en la propiedad
        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month);
        }
        //Retornamos la consulta
        return $query->orderBy('folio', 'desc')->paginate(10);
    }

    public function detallesTraspaso($folio)
    {
        //Folio seleccionado
        $this->traspaso_seleccionado = $folio;
        //Obtenemos los detalles del traspaso
        $this->traspaso_detalles = DetalleTraspasoNew::where('folio_traspaso', $folio)->get();
        //Emitir evento para abrir el modal
        $this->dispatch('open-modal', name: 'modal-traspaso');
    }

    public function render()
    {
        return view('livewire.almacen.traspasos.v2.principal');
    }
}

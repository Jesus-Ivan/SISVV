<?php

namespace App\Livewire\Almacen\Entradas\V2;

use App\Models\DetalleEntradaNew;
use App\Models\DetallesEntrada;
use App\Models\EntradaNew;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;


class Principal extends Component
{
    use WithPagination;
    public $mes_busqueda;

    public $entrada_seleccionada, $entrada_detalles = [];

    public function mount()
    {
        $this->mes_busqueda = now()->format('Y-m');
    }
    #[Computed()]
    public function entradas()
    {
        //Preparar el constructor de consultas
        $query = EntradaNew::query();
        //Verificar si hay mes en la propiedad
        if ($this->mes_busqueda) {
            $year = substr($this->mes_busqueda, 0, 4);
            $month = substr($this->mes_busqueda, 5, 2);
            //Agregar condiciones a la consulta
            $query->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month);
        }
        return $query->orderBy('fecha_existencias', "DESC")->paginate(10);
    }

    /**
     * Se ejecuta para buscar las ordenes.
     */
    public function buscar()
    {
        //Reinicia el paginador.
        $this->resetPage();
    }

    public function verDetalles($folio)
    {
        //Establecer el folio seleccionado
        $this->entrada_seleccionada = $folio;
        //Buscar los detalles de la nueva entrada
        $this->entrada_detalles = DetalleEntradaNew::where('folio_entrada', $folio)->get();
        //Emitir evento para abrir el modal
        $this->dispatch('open-modal', name: 'modal-entrada');
        
    }

    public function render()
    {
        return view('livewire.almacen.entradas.v2.principal');
    }
}

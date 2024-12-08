<?php

namespace App\Livewire\Almacen\Salidas;

use App\Models\DetallesSalida;
use App\Models\Salida;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $fSearch;

    #[Locked]
    public $salida_seleccionada, $salida_detalles = [];

    public function mount()
    {
        $this->fSearch = now()->day(1)->toDateString();
    }

    public function buscar()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function salidas()
    {
        $today = Carbon::parse($this->fSearch);

        return Salida::with('bodegaOrigen', 'destino')
            ->whereMonth('fecha', '=', $today->month)
            ->whereYear("fecha", '=', $today->year)
            ->orderBy('folio', 'DESC')
            ->paginate(10);
    }


    public function verDetalles($folio)
    {
        $this->salida_seleccionada = $folio;
        $this->salida_detalles = DetallesSalida::where('folio_salida', $folio)->get();
        $this->dispatch('open-modal', name: 'modal-salida');
    }

    public function render()
    {
        return view('livewire.almacen.salidas.principal');
    }
}

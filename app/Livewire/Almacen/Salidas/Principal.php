<?php

namespace App\Livewire\Almacen\Salidas;

use App\Models\Salida;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $fSearch;

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
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.almacen.salidas.principal');
    }
}

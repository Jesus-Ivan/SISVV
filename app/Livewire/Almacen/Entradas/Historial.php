<?php

namespace App\Livewire\Almacen\Entradas;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Historial extends Component
{
    use WithPagination;

    #[Computed()]
    public function detalles()
    {
        return DB::table('detalles_entradas')
            ->join('entradas', 'detalles_entradas.folio_entrada', '=', 'entradas.folio')
            ->paginate(20);
    }
    public function render()
    {
        return view('livewire.almacen.entradas.historial');
    }
}

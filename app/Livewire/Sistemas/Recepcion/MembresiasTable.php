<?php

namespace App\Livewire\Sistemas\Recepcion;

use App\Models\Membresias;
use Livewire\Component;
use Livewire\WithPagination;

class MembresiasTable extends Component
{
    use WithPagination;
    public $search;
    public Membresias $acciones;

    public function render()
    {
        return view('livewire.sistemas.recepcion.membresias-table', [
            'listaMembresias' => Membresias::where('descripcion', 'like', '%' . $this->search . '%')->orWhere('clave', '=', $this->search)
                ->paginate(5)
        ]);
    }
}

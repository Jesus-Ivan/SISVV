<?php

namespace App\Livewire\Almacen\Salidas;

use App\Models\Salida;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public Salida $salidas;
    public $search;

    public function render()
    {
        return view('livewire.almacen.salidas.principal', [
            'listaSalidas' => Salida::where('fecha', 'like', '%' . $this->search . '%')
            ->paginate(10)
        ]);
    }
}

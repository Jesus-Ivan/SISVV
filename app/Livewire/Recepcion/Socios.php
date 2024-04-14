<?php

namespace App\Livewire\Recepcion;

use App\Models\Socio;
use Livewire\Component;
use Livewire\WithPagination;

class Socios extends Component
{
    use WithPagination;
    public $search;

    public function render()
    {
        return view('livewire.recepcion.socios', [
            'listaSocios' => Socio::where('nombre', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(5)
        ]);
    }
}

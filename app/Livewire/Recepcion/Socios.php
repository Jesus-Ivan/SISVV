<?php

namespace App\Livewire\Recepcion;

use App\Models\Socio;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Socios extends Component
{
    use WithPagination;
    public $search;

    public function updated($search){
        $this->resetPage();
    }

    public function render()
    {
        $result = DB::table('socios')
            ->join('membresias', 'socios.clave_membresia', '=', 'membresias.clave')
            ->where('nombre', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
            ->orderByDesc('socios.id')
            ->paginate(5);

        return view('livewire.recepcion.socios', [
            'listaSocios' => $result
        ]);
    }
}

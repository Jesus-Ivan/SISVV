<?php

namespace App\Livewire\Recepcion;

use App\Models\Socio;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

use function Laravel\Prompts\select;

class Socios extends Component
{
    use WithPagination;
    public $search;

    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        $result = DB::table('socios')
            ->join('socios_membresias', 'socios.id', '=', 'socios_membresias.id_socio')
            ->join('membresias', 'socios_membresias.clave_membresia', '=', 'membresias.clave')
            ->select('socios.*', 'membresias.descripcion')
            ->where('socios.nombre', 'like', '%' . $this->search . '%')
            ->orwhere('socios.apellido_p', 'like', '%' . $this->search . '%')
            ->orwhere('socios.apellido_m', 'like', '%' . $this->search . '%')
            ->orWhere('socios.id', '=', $this->search)
            ->orderByDesc('socios.id')
            ->paginate(5);

        return view('livewire.recepcion.socios', [
            'listaSocios' => $result
        ]);
    }
}

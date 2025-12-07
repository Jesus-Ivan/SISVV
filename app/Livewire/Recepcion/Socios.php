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
        $result = Socio::query()
            ->with('socioMembresia.membresia')
            ->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orwhere('apellido_p', 'like', '%' . $this->search . '%')
                    ->orwhere('apellido_m', 'like', '%' . $this->search . '%')
                    ->orWhere('id', '=', $this->search);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.recepcion.socios', [
            'listaSocios' => $result
        ]);
    }
}

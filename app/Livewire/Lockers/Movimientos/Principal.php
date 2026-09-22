<?php

namespace App\Livewire\Lockers\Movimientos;

use App\Models\MovimientoLocker;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    //Propiedades para la busqueda de movimientos
    public $search = '';
    public $date_search;

    //Hook al inicio de vida del componente
    public function mount()
    {
        $this->date_search = now()->toDateString();
    }

    #[Computed()]
    public function movimientos()
    {
        $result = MovimientoLocker::with('locker')
            ->whereAny(
                ['id_socio', 'nombre', 'observaciones'],
                'like',
                "%$this->search%"
            )
            ->whereDate('fecha_movimiento', $this->date_search)
            ->paginate(10);
        return $result;
    }

    public function render()
    {
        return view('livewire.lockers.movimientos.principal');
    }
}

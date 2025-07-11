<?php

namespace App\Livewire\Almacen\Requisiciones;

use App\Models\Requisicion;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $f_inicio, $f_fin;
    public $order = true;

    //Hook que se ejecuta una vez al inicio de vida del componente
    public function mount()
    {
        $this->f_inicio = now()->day(1)->toDateString();
        $this->f_fin = now()->day(now()->daysInMonth)->toDateString();
    }

    //Propiedad computarizada, que almacena las ordenes buscadas
    #[Computed()]
    public function ordenes()
    {
        return Requisicion::whereDate('created_at', '>=', $this->f_inicio)
            ->whereDate('created_at', '<=', $this->f_fin)
            ->orderBy('folio', 'desc')
            ->paginate(10);
    }

    /**
     * Se ejecuta para buscar las ordenes.
     */
    public function buscar()
    {
        //Reinicia el paginador.
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.requisiciones.principal');
    }
}

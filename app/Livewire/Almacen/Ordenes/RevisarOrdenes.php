<?php

namespace App\Livewire\Almacen\Ordenes;

use App\Models\OrdenCompra;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class RevisarOrdenes extends Component
{
    use WithPagination;
    public $f_inicio, $f_fin;

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
        return OrdenCompra::whereDate('fecha', '>=', $this->f_inicio)
            ->whereDate('fecha', '<=', $this->f_fin)
            ->paginate(10);
    }

    /**
     * Se ejecuta para buscar las ordenes.
     */
    public function buscar(){
        //Reinicia el paginador.
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.almacen.ordenes.revisar-ordenes');
    }
}
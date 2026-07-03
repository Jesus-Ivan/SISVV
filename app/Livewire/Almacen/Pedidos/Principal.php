<?php

namespace App\Livewire\Almacen\Pedidos;

use App\Models\SolicitudPedido;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search_mes;

    // Inicializar propiedades al cargar el componente
    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    #[Computed()]
    public function pedidos()
    {
        $query = SolicitudPedido::query()
            ->with('bodegaOrigen');

        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month);
        }

        return $query->orderBy('fecha_existencias', 'asc')->paginate(10);
    }

    public function render()
    {
        return view('livewire.almacen.pedidos.principal');
    }
}

<?php

namespace App\Livewire\Puntos\Inventario;

use App\Models\DetallesSolicitudPedido;
use App\Models\SolicitudPedido;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitarMercancia extends Component
{
    use WithPagination;
    public $search_mes;

    #[Locked]
    public $pedido_seleccionado, $pedido, $detalles_pedido = [];

    // Inicializar propiedades al cargar el componente
    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    #[Computed()]
    public function pedidos()
    {
        $query = SolicitudPedido::query();

        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month)
                ->orderBy('fecha_existencias', 'desc');
        }

        return $query->paginate(10);
    }


    public function detallesPedido($folio)
    {   
        $this->pedido_seleccionado = $folio;
        $this->pedido = SolicitudPedido::where('folio', $folio)->first();
        $this->detalles_pedido = DetallesSolicitudPedido::where('folio_pedido', $folio)->get();
        $this->dispatch('open-modal', name: 'detalles');
    }

    public function render()
    {
        return view('livewire.puntos.inventario.solicitar-mercancia');
    }
}

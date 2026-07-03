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
    public $codigopv;

    #[Locked]
    public $pedido_seleccionado, $pedido, $detalles_pedido = [];

    // Inicializar propiedades al cargar el componente
    public function mount($codigopv)
    {
        $this->search_mes = now()->format('Y-m');
        $this->codigopv = $codigopv;
    }

    #[Computed()]
    public function pedidos()
    {
        $claveBodega = "stock_" . strtolower($this->codigopv);

        $query = SolicitudPedido::query()
            ->where('clave_pv', $claveBodega);

        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month);
        }

        return $query->orderBy('fecha_existencias', 'desc')->paginate(10);
    }

    //Muestra los detalles de las solicitudes de mercancia
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

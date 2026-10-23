<?php

namespace App\Livewire\Puntos\Inventario;

use App\Models\DetallesSolicitudPedido;
use App\Models\DetalleTraspasoNew;
use App\Models\MovimientosAlmacen;
use App\Models\SolicitudPedido;
use App\Models\TraspasoNew;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitarMercancia extends Component
{
    use WithPagination;
    public $search_mes;
    public $codigopv;
    public $detalles_traspaso = [];
    public $existencias_antes_traspaso = [];

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
        $this->existencias_antes_traspaso = [];

        //Verificamos si existe un folio de traspaso para mostrar la cantidad traspasada
        if ($this->pedido && $this->pedido->folio_traspaso > 0) {
            $this->detalles_traspaso = DetalleTraspasoNew::where('folio_traspaso', $this->pedido->folio_traspaso)
                ->get()
                ->keyBy('clave_insumo');

            $traspaso = TraspasoNew::find($this->pedido->folio_traspaso);
            if ($traspaso) {
                $existencias = MovimientosAlmacen::query()
                    ->select('clave_insumo')
                    ->selectRaw('SUM(cantidad_insumo) as existencias')
                    ->where('clave_bodega', $traspaso->clave_destino)
                    ->where('fecha_existencias', '<=', $traspaso->fecha_existencias)
                    ->whereNotNull('clave_insumo')
                    ->where(function ($query) use ($traspaso) {
                        $query->whereNull('folio_traspaso')
                            ->orWhere('folio_traspaso', '<>', $traspaso->folio);
                    })
                    ->groupBy('clave_insumo')
                    ->pluck('existencias', 'clave_insumo');

                $this->existencias_antes_traspaso = $this->detalles_traspaso
                    ->keys()
                    ->mapWithKeys(fn ($clave) => [$clave => $existencias[$clave] ?? 0])
                    ->all();
            }
        } else {
            $this->detalles_traspaso = collect();
        }

        $this->dispatch('open-modal', name: 'detalles');
    }

    public function render()
    {
        return view('livewire.puntos.inventario.solicitar-mercancia');
    }
}

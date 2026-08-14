<?php

namespace App\Livewire\Puntos\Ventas\Transferir;

use App\Models\Caja;
use App\Models\CorreccionVenta;
use App\Models\DetallesVentaProducto;
use App\Models\MotivoCorreccion;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Container extends Component
{
    public Venta $venta;

    #[Locked]
    public $productos = [];
    #[Locked]
    public $cajas = [];

    public $search;
    public $caja_destino, $folio_destino;

    //Hook al inicio el vida el componente
    public function mount()
    {
        //Buscar los productos
        $this->productos = DetallesVentaProducto::where('folio_venta', $this->venta->folio)
            ->get()
            ->toArray();

        //Buscar lsa cajas abiertas
        $this->cajas = Caja::with('puntoVenta')
            ->whereNull('fecha_cierre')
            ->get()->toArray();
    }

    #[Computed()]
    public function ventas()
    {
        //Buscar ventas abiertas en la caja de destino
        $result = Venta::whereAny(['id_socio', 'nombre'], 'like', '%' . $this->search . '%')
            ->where('corte_caja', $this->caja_destino)
            ->whereNull('fecha_cierre')
            ->orderby('fecha_apertura', 'desc')
            ->get();
        return $result;
    }

    public function marcar($folio)
    {
        if ($folio != $this->venta->folio) {
            $this->folio_destino = $folio;
        }
    }


    public function confirmarTransferencia(bool $unificar)
    {
        if ($unificar) {
            //Rectificar cuenta de origen
            //Rectificar cuenta de destino
            $this->fusionarCuenta();
        } else {
            $this->moverCuenta();
        }
    }

    /**
     * Mueve la venta de un punto a otro
     */
    public function moverCuenta()
    {
        //Obtener la caja
        $caja = Caja::where('corte', $this->caja_destino)
            ->whereNull('fecha_cierre')
            ->first();
        //Obtener los productos
        $new_productos = DetallesVentaProducto::where('folio_venta', $this->venta->folio)
            ->get()
            ->toArray();

        if (!$caja) {
            throw new Exception("La caja fue cerrada", 1);
        }
        if (count($new_productos) != count($this->productos)) {
            throw new Exception("Otro usuario modifico la venta", 1);
        }


        DB::transaction(function (){
            /**
             * Mover la venta de punto y de corte
             */
            $this->venta->corte_caja = $this->caja_destino->corte;
            $this->venta->clave_punto_venta = $this->caja_destino->clave_punto_venta;
            $this->venta->save();

            /**
             * Crear el registro en la tabla 'correcciones_ventas'
             */
            //Buscar el motivo de la correccion
            $motivo = MotivoCorreccion::where('descripcion', 'like', '%MOVER PUNTO DE VENTA%')->first();
            //Crear el registro en la tabla 'correcciones_ventas'
            CorreccionVenta::create([
                'user_name' => auth()->user()->name,
                'folio_venta' => $this->venta->folio,
                'tipo_venta' => $this->venta->tipo_venta,
                'solicitante_name' => auth()->user()->name,
                'id_motivo' => $motivo->id,
            ]);
        });
    }

    /**
     * Mueve la venta y la fusiona con otra venta
     */
    public function fusionarCuenta() {}

    public function render()
    {
        return view('livewire.puntos.ventas.transferir.container');
    }
}

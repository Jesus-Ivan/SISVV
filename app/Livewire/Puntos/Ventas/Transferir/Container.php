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
            $this->unificarCuentas();
        } else {
            $this->moverCuenta();
        }
    }

    public function unificarCuentas()
    {
        try {
            DB::transaction(function () {
                //Rectificar cuenta de origen
                $this->verificarVenta('origen', $this->venta->folio);
                //Rectificar cuenta de destino
                $this->verificarVenta('destino', $this->folio_destino);

                $this->fusionarCuenta($this->venta->folio, $this->folio_destino);
            });
            //Mensaje de exito en el alert
            session()->flash('success', "VENTA UNIFICADA CORRECTAMENTE");
            // Cerrar ventana
            $this->dispatch('cerrar-pagina');
        } catch (\Throwable $th) {
            //Mensaje de error en el alert
            session()->flash('fail', $th->getMessage());
        }
        //Evento para mostrar alert
        $this->dispatch('action-message-venta');
    }

    /**
     * Mueve la venta de un punto a otro
     */
    public function moverCuenta()
    {
        try {
            //Obtener la caja de destino
            $caja = Caja::where('corte', $this->caja_destino)
                ->whereNull('fecha_cierre')
                ->first();
            //Obtener los productos
            $new_productos = DetallesVentaProducto::where('folio_venta', $this->venta->folio)
                ->get()
                ->toArray();
            //Obtener denuevo la venta
            $venta = Venta::find($this->venta->folio);

            if (!$caja) {
                throw new Exception("La caja fue cerrada", 1);
            }
            if (count($new_productos) != count($this->productos)) {
                throw new Exception("Otro usuario modifico la venta", 1);
            }

            if (!$venta) {
                throw new Exception("La venta ya fue fusionada anteriormente: " . $this->venta->folio, 1);
            }

            DB::transaction(function () use ($caja, $venta) {

                /**
                 * Mover la venta de punto y de corte
                 */
                $venta->corte_caja = $caja->corte;
                $venta->clave_punto_venta = $caja->clave_punto_venta;
                $venta->save();

                /**
                 * Crear el registro en la tabla 'correcciones_ventas'
                 */
                //Buscar el motivo de la correccion
                $motivo = MotivoCorreccion::where('descripcion', 'like', '%MOVER PUNTO DE VENTA%')->first();
                //Crear el registro en la tabla 'correcciones_ventas'
                CorreccionVenta::create([
                    'user_name' => auth()->user()->name,
                    'folio_venta' => $venta->folio,
                    'tipo_venta' => $venta->tipo_venta,
                    'solicitante_name' => auth()->user()->name,
                    'id_motivo' => $motivo->id,
                ]);
            });

            //Mensaje de exito en el alert
            session()->flash('success', "VENTA TRANSFERIDA CORRECTAMENTE");
            // Cerrar ventana
            $this->dispatch('cerrar-pagina');
        } catch (\Throwable $th) {
            //Mensaje de error en el alert
            session()->flash('fail', $th->getMessage());
        }
        //Evento para mostrar alert
        $this->dispatch('action-message-venta');
    }


    /**
     * Verifica la cuenta no haya sido eliminada/modificada/cerrada\
     * Segun si se trata de la venta de origen o destino
     */
    public function verificarVenta(string $type, $folio)
    {
        //Obtener la venta y verificar su existencia
        $venta = Venta::find($folio);
        if (!$venta) {
            throw new Exception("La venta ya fue fusionada por otro usuario", 1);
        }

        if ($type == 'origen') {
            //Obtener los productos y verificar la cantidad de los mismos
            $new_productos = DetallesVentaProducto::where('folio_venta', $folio)
                ->get()
                ->toArray();
            if (count($new_productos) != count($this->productos)) {
                throw new Exception("Otro usuario modifico la venta de $type: " . $folio, 1);
            }
        }

        //revisar si la venta esta cerrada
        if (!is_null($venta->fecha_cierre)) {
            throw new Exception("La venta de $type ya esta cerrada: " . $folio, 1);
        }
    }


    /**
     * Mueve la venta y la fusiona con otra venta
     */
    public function fusionarCuenta($folio_origen, $folio_destino)
    {

        /**
         * Obtener y verificar la caja de destino
         */
        $caja = Caja::where('corte', $this->caja_destino)
            ->whereNull('fecha_cierre')
            ->first();

        if (!$caja) {
            throw new Exception("La caja fue cerrada", 1);
        }

        /**
         * Eliminar la venta de origen
         */
        $venta_origen = Venta::find($folio_origen);
        $venta_origen->delete();

        //Reasignar los productos al folio de destino
        DetallesVentaProducto::where('folio_venta', $folio_origen)
            ->update(['folio_venta' => $folio_destino]);

        /**
         * Actualizar el total de la venta destino
         */
        //Obtener todos los productos
        $productos = DetallesVentaProducto::where('folio_venta', $folio_destino)
            ->get()
            ->toArray();
        //Obtener el nuevo total
        $new_total = array_sum(array_column($productos, 'subtotal'));
        $venta_destino = Venta::find($folio_destino);
        $venta_destino->total = $new_total;
        $venta_destino->save(); //Actualizar total

        /**
         * Crear el registro en la tabla 'correcciones_ventas'
         */
        //Buscar el motivo de la correccion
        $motivo = MotivoCorreccion::where('descripcion', 'like', '%FUSIONAR%')->first();
        //Crear el registro en la tabla 'correcciones_ventas'
        CorreccionVenta::create([
            'user_name' => auth()->user()->name,
            'folio_venta' => $this->venta->folio,
            'tipo_venta' => $this->venta->tipo_venta,
            'solicitante_name' => auth()->user()->name,
            'id_motivo' => $motivo->id,
            'folio_destino' => $folio_destino
        ]);
    }

    public function render()
    {
        return view('livewire.puntos.ventas.transferir.container');
    }
}

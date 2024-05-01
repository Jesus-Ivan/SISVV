<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Container extends Component
{
    public $datosSocio = [];
    public $datosProductos = [];
    public $datosPagos = [];

    #[Computed()]
    public function puntoVenta()
    {
        //Obtenemos el punto de venta actual
        return PuntoVenta::where('nombre', 'LIKE', '%RECEP%')
        ->limit(1)
        ->get()[0];
    }

    public function cerrarVenta()
    {
        $info = $this->validate([
            'datosSocio' => 'min:1',
            'datosProductos' => 'min:1',
            'datosPagos' => 'min:1',
        ]);

        //Consultamos la BD para obtener la caja abierta
        $resultCaja = Caja::where('fecha_cierre', null)
            ->where('id_usuario', auth()->user()->id)
            ->where('clave_punto_venta', $this->puntoVenta->clave)
            ->limit(1)
            ->get();

        //Comprobamos las cajas abiertas
        if (count($resultCaja) == 1) {
            //Se crea la transaccion
            DB::transaction(function () use ($resultCaja, $info) {
                //Obtenemos la fecha-hora de cierre actual, con una instancia de Carbon.
                $fecha_cierre = now()->format('Y-m-d H:i:s');
                //Se calcular el total de los productos
                $total = array_sum(array_column($info['datosProductos'], 'subtotal'));
                //Se crea la venta
                $resultVenta = Venta::create([
                    'id_socio' => $info['datosSocio']['id'],
                    'nombre' => $info['datosSocio']['nombre'],
                    'fecha_apertura' => $fecha_cierre,
                    'fecha_cierre' => $fecha_cierre,
                    'total' => $total,
                    'id_tipo_pago' => 0,
                    'status' => false,
                    'corte_caja' => $resultCaja[0]->corte,
                ]);
                //Se crea el detalle de la venta
                foreach ($info['datosProductos'] as $key => $producto) {
                    DetallesVentaProducto::create([
                        'folio_venta' => $resultVenta->folio,
                        'codigo_venta_producto' => $producto['codigo_venta_producto'],
                        'cantidad' => $producto['cantidad'],
                        'precio' => $producto['precio'],
                        'subtotal' => $producto['subtotal'],
                        'inicio' => $producto['inicio'],
                    ]);
                }
                //Se crea el detalle de los pagoss
                foreach ($info['datosPagos'] as $key => $pago) {
                    DetallesVentaPago::create([
                        'folio_venta' => $resultVenta->folio,
                        'id_socio' => $pago['id_socio'],
                        'nombre' => $pago['nombre'],
                        'monto' => $pago['monto'],
                        'propina' => $pago['propina'],
                        'id_tipo_pago' => $pago['id_tipo_pago'],
                    ]);
                }
            });
            session()->flash('success', "Se registro la venta correctamente");
            //Se limpian los datos
            $this->reset();
        } else {
            //Si no hay cajas abiertas
            session()->flash('fail', "No hay caja abierta");
        }
        $this->dispatch('action-message-venta');
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.container');
    }
}

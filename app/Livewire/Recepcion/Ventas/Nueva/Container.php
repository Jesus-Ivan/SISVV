<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\Socio;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Container extends Component
{
    public  $datosSocio = [];
    public  $datosProductos = [];
    public  $datosPagos = [];

    public function cerrarVenta()
    {
        $info = $this->validate([
            'datosSocio' => 'min:1',
            'datosProductos' => 'min:1',
            'datosPagos' => 'min:1',
        ]);

        $resultCaja = Caja::where('fecha_cierre', null)
            ->where('id_usuario', auth()->user()->id)
            ->take(1)
            ->get();

        //Si no hay caja abierta
        if (count($resultCaja) == 1) {

            //Se crea la venta
            DB::transaction(function () use ($resultCaja, $info) {
                $fecha_cierre = now()->format('Y-m-d H:i:s');
                $total = array_sum(array_column($info['datosProductos'], 'subtotal'));
                $resultVenta = Venta::create([
                    'id_socio' => $info['datosSocio']['id'],
                    'nombre' => $info['datosSocio']['nombre'],
                    'fecha_apertura' => $fecha_cierre,
                    'fecha_cierre' => $fecha_cierre,
                    'total' => $total,
                    'id_tipo_pago' => 0,
                    'clave_punto_venta' => 'pendi',
                    'status' => false,
                    'corte_caja' => $resultCaja[0]->corte,
                ]);
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
        } else {
            session()->flash('fail', "No hay caja abierta");
        }
        $this->dispatch('action-message-venta');
    }

    public function showData()
    {
        dump($this->datosSocio, $this->datosPagos, $this->datosProductos);
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.container');
    }
}

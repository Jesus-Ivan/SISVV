<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Container extends Component
{
    public $datosSocio = [];
    public $datosProductos = [];
    public $datosPagos = [];
    #[Locked]
    public $invitado = false;
    #[Locked]
    public $pv;

    public function cerrarVenta()
    {
        $info = $this->validate([
            'datosSocio' => 'min:1',
            'datosProductos' => 'min:1',
            'datosPagos' => 'min:1',
        ]);
        //Calculamos total de los productos
        $montoTotalVenta = array_sum(array_column($this->datosProductos, 'subtotal'));
        //Calculamos total de los pagos
        $montoTotalPago = array_sum(array_column($this->datosPagos, 'monto'));
        //Si los totales no coinciden, error.
        if ($montoTotalPago != $montoTotalVenta) {
            //Mandamos mensaje de sesion al alert
            session()->flash('fail', "El monto total de pago no es el correcto");
            //Emitimos evento para abrir el alert
            $this->dispatch('action-message-venta');
            return;
        }

        //Consultamos la BD para obtener la caja abierta
        $resultCaja = Caja::where('fecha_cierre', null)
            ->where('id_usuario', auth()->user()->id)
            ->where('clave_punto_venta', $this->pv->clave)
            ->limit(1)
            ->get();

        //Comprobamos las cajas abiertas
        if (count($resultCaja) == 1) {
            //Si no es invitado, hacer venta normal
            if (!$this->invitado) {
                //Se crea la transaccion
                $this->registrarVenta($resultCaja, $info);
                $this->reset('datosSocio', 'datosProductos', 'datosPagos');
            } else {
                //Si es invitado, se crea una venta de invitado
                $this->registrarVentaInvitado($resultCaja, $info);
                $this->reset('datosProductos', 'datosPagos');
            }
            session()->flash('success', "Se registro la venta correctamente");
        } else {
            //Si no hay cajas abiertas
            session()->flash('fail', "No hay caja abierta");
        }
        $this->dispatch('action-message-venta');
    }

    private function registrarVenta($resultCaja, $info)
    {
        DB::transaction(function () use ($resultCaja, $info) {
            //Obtenemos la fecha-hora de cierre actual, con una instancia de Carbon.
            $fecha_cierre = now()->format('Y-m-d H:i:s');
            //Se calcular el total de los productos
            $total = array_sum(array_column($info['datosProductos'], 'subtotal'));
            //Concatenamos el nombre
            $nombre = $info['datosSocio']['nombre'] . ' ' . $info['datosSocio']['apellido_p'] . ' ' . $info['datosSocio']['apellido_m'];
            //Se crea la venta
            $resultVenta = Venta::create([
                'id_socio' =>  $info['datosSocio']['id'],
                'nombre' => $nombre,
                'fecha_apertura' => $fecha_cierre,
                'fecha_cierre' => $fecha_cierre,
                'total' => $total,
                'corte_caja' => $resultCaja[0]->corte,
                'clave_punto_venta' => $this->pv->clave,
                'tipo_venta' => 'socio'
            ]);
            //Se crea el detalle de la venta
            foreach ($info['datosProductos'] as $key => $producto) {
                DetallesVentaProducto::create([
                    'folio_venta' => $resultVenta->folio,
                    'clave_producto' => $producto['clave_producto'],
                    'chunk' =>$producto['chunk'],
                    'nombre' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal'],
                    'inicio' => now()->format('Y:m:d H:m:s'),
                ]);
            }
            //Se crea el detalle de los pagos
            foreach ($info['datosPagos'] as $key => $pago) {
                $resultPago = DetallesVentaPago::create([
                    'folio_venta' => $resultVenta->folio,
                    'id_socio' => $pago['id_socio'],
                    'nombre' => $pago['nombre'],
                    'monto' => $pago['monto'],
                    'propina' => $pago['propina'],
                    'id_tipo_pago' => $pago['id_tipo_pago'],
                ]);
                //Verificamos si el tipo de pago es firma
                if (strcasecmp('FIRMA', $pago['descripcion_tipo_pago']) == 0) {
                    //Creamos el concepto en el estado de cuenta (con abono 0)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $resultVenta->folio . ' - ' . $this->pv->nombre,
                        'cargo' => $pago['monto'],
                        'saldo' => $pago['monto'],
                        'fecha' => $fecha_cierre,
                    ]);
                    //Verificamos si tiene propina
                    if ($pago['propina']) {
                        //Creamos el concepto de la propina en el estado de cuenta
                        EstadoCuenta::create([
                            'id_socio' => $pago['id_socio'],
                            'id_venta_pago' => $resultPago->id,
                            'concepto' => 'PROPINA NOTA VENTA: ' . $resultVenta->folio . ' - ' . $this->pv->nombre,
                            'fecha' => now()->toDateString(),
                            'cargo' => $pago['propina'],
                            'saldo' => $pago['propina'],
                            'consumo' => false
                        ]);
                    }
                } else {
                    //Creamos el concepto en el estado de cuenta (con abono total)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $resultVenta->folio . ' - ' . $this->pv->nombre,
                        'cargo' => $pago['monto'],
                        'abono' => $pago['monto'],
                        'saldo' => 0,
                        'fecha' => $fecha_cierre,
                    ]);
                }
            }

            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $resultVenta->folio]);
        });
    }

    private function registrarVentaInvitado($resultCaja, $info)
    {
        DB::transaction(function () use ($resultCaja, $info) {
            //Obtenemos la fecha-hora de cierre actual, con una instancia de Carbon.
            $fecha_cierre = now()->format('Y-m-d H:i:s');
            //Se calcular el total de los productos
            $total = array_sum(array_column($info['datosProductos'], 'subtotal'));
            //Se crea la venta
            $resultVenta = Venta::create([
                'nombre' => $info['datosSocio']['nombre'],
                'fecha_apertura' => $fecha_cierre,
                'fecha_cierre' => $fecha_cierre,
                'total' => $total,
                'corte_caja' => $resultCaja[0]->corte,
                'clave_punto_venta' => $this->pv->clave,
                'tipo_venta' => 'general'
            ]);
            //Se crea el detalle de la venta
            foreach ($info['datosProductos'] as $key => $producto) {
                DetallesVentaProducto::create([
                    'folio_venta' => $resultVenta->folio,
                    'clave_producto' => $producto['clave_producto'],
                    'chunk' =>$producto['chunk'],
                    'nombre' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal'],
                    'inicio' => now()->format('Y:m:d H:m:s'),
                ]);
            }
            //Se crea el detalle de los pagos
            foreach ($info['datosPagos'] as $key => $pago) {
                $resultPago = DetallesVentaPago::create([
                    'folio_venta' => $resultVenta->folio,
                    'nombre' => $pago['nombre'],
                    'monto' => $pago['monto'],
                    'propina' => $pago['propina'],
                    'id_tipo_pago' => $pago['id_tipo_pago'],
                ]);
            }
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $resultVenta->folio]);
        });
    }

    #[On('on-invitado')]
    public function onInvitado(bool $val, $invitado)
    {
        $this->invitado = $val;
        //Borrar los pagos registrados
        $this->reset('datosPagos');
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.container');
    }
}

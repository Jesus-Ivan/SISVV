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
            ->where('clave_punto_venta', $this->puntoVenta->clave)
            ->limit(1)
            ->get();

        //Comprobamos las cajas abiertas
        if (count($resultCaja) == 1) {
            //Si no es invitado, hacer venta normal
            if (!$this->invitado) {
                //Se crea la transaccion
                $this->registrarVenta($resultCaja, $info);
            } else {
                //Si es invitado, se crea una venta de invitado
                $this->registrarVentaInvitado($resultCaja, $info);
            }
            session()->flash('success', "Se registro la venta correctamente");
            //Se re-establece el atributo que guarda el estado del switch de venta 'Invitado'
            $this->invitado = false;
            //Se limpian los datos
            $this->reset();
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
            ]);
            //Se crea el detalle de la venta
            foreach ($info['datosProductos'] as $key => $producto) {
                DetallesVentaProducto::create([
                    'folio_venta' => $resultVenta->folio,
                    'codigo_catalogo' => $producto['codigo_catalogo'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal'],
                    'inicio' => $producto['inicio'],
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
                        'concepto' => 'Nota venta: ' . $resultVenta->folio . ' - ' . $this->puntoVenta->nombre,
                        'cargo' => $pago['monto'],
                        'saldo' => $pago['monto'],
                        'fecha' => $fecha_cierre,
                    ]);
                } else {
                    //Creamos el concepto en el estado de cuenta (con abono total)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'Nota venta: ' . $resultVenta->folio . ' - ' . $this->puntoVenta->nombre,
                        'cargo' => $pago['monto'],
                        'abono' => $pago['monto'],
                        'saldo' => 0,
                        'fecha' => $fecha_cierre,
                    ]);
                }
            }
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
            ]);
            //Se crea el detalle de la venta
            foreach ($info['datosProductos'] as $key => $producto) {
                DetallesVentaProducto::create([
                    'folio_venta' => $resultVenta->folio,
                    'codigo_catalogo' => $producto['codigo_catalogo'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal'],
                    'inicio' => $producto['inicio'],
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

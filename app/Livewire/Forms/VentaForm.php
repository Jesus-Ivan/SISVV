<?php

namespace App\Livewire\Forms;

use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\PuntoVenta;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaForm extends Form
{
    public $tipoVenta = "socio";    //El tipo de venta a realizar
    public $invitado;               //Invitado
    public $nombre_invitado;        //El nombre del invitado
    public $socioSeleccionado;      //El socio seleccionado
    public $socio = [];             //Datos del socio

    public $socioPago;              //El socio seleccionado para agregar en metodo de pago
    public $id_pago;                //id del tipo de pago seleccionado en el modal
    public $pagos = [];
    public $monto_pago;             //el monto a pagar
    public $propina;               //Si dejo o no propina

    public $seachProduct = '';        //Input de busqueda de productos
    public $selected = [];            //Almacena los codigos de productos seleccionados del modal.

    public $productosTable = [];      //array de productos, que se muestran en la tabla
    public $pagosTable = [];          //Array de pagos que se muestran en la tabla
    public $totalVenta = 0;           //El costo total de los articulos
    public $totalPago = 0;            //El total de los pagos
    public $totalPropina = 0;         //El total de la propina
    public $descuento = 0;          //En caso de que se aplique un descuento a la cuenta
    public $totalSinDescuento = 0;  //Centa total temporal en caso que se le aplique un descuento
    public $totalConDescuento = 0;  //El total de la venta final
    public $cambio = 0;
    
    /* Agrega los articulos seleccionados a la tabla.
        La funcion recibe el array de todos los items mostrado en el modal.
    */
    public function agregarItems($productosResult)
    {
        //Filtramos los productos seleccionados, cuyo valor sea true del checkBox
        $total_seleccionados = array_filter($this->selected, function ($val) {
            return $val;
        });

        //Si no se han seleccionado articulos, impedir ejecuccion
        if (!count($total_seleccionados) > 0) {
            return false;
        }
        //Recorrer todo el array de seleccionados
        foreach ($total_seleccionados as $key => $value) {
            //Se busca el registro del producto en base a su codigo.
            $producto = $productosResult->find($key);
            //Se anexa el producto al array de la tabla
            $this->productosTable[] = [
                'codigo_catalogo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio' => $producto->costo_unitario,
                'subtotal' => $producto->costo_unitario,
                'observaciones' => '',
                'tiempo' => null
            ];
        }

        //Limpiamos las propiedades
        $this->selected = [];    //Productos seleccionados
        $this->actualizarTotal();
    }

    public function eliminarArticulo($productoIndex)
    {
        unset($this->productosTable[$productoIndex]);
        $this->actualizarTotal();
    }

    public function calcularSubtotal($productoIndex, $eValue)
    {
        //Se actualiza la cantidad del producto en la tabla
        $this->productosTable[$productoIndex]['cantidad'] = $eValue;
        //Se calcula el subtotal del producto
        $this->productosTable[$productoIndex]['subtotal'] = $this->productosTable[$productoIndex]['precio'] * $eValue;
        $this->actualizarTotal();
    }

    public function actualizarTotal()
    {
        //Se actualiza el total de los productos
        $this->totalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
    }

    public function agregarPago()
    {
        $validation_rules = [
            //'metodo_pago' => 'required',
            'monto_pago' => 'required|numeric',
        ];
        //Validamos las entradas
        $validated = $this->validate($validation_rules);

        //Se agrega el pago a la tabla de pagos
        $this->pagosTable[] = [
            'id_socio' => $this->socioPago->id,
            'nombre' => $this->socioPago->nombre .' '. $this->socioPago->apellido_p .' '. $this->socioPago->apellido_m,
            'id_tipo_pago' => $this->id_pago,
            'descripcion_tipo_pago' => TipoPago::find($this->id_pago)->descripcion,
            'monto_pago' => $validated['monto_pago'],
            'propina' => $this->propina,
        ];
        $this->reset('id_socio', 'id_tipo_pago', 'monto_pago', 'propina');
        $this->actualizarPago();
    }

    public function eliminarPago($pagoIndex)
    {
        unset($this->pagosTable[$pagoIndex]);
        $this->actualizarPago();
    }

    public function actualizarPago()
    {
        //Se actualiza el total del pago
        $this->totalSinDescuento = array_sum(array_column($this->pagosTable, 'monto_pago'));

        //Aplicar descuento si es que existe
        /* if ($this->descuento > 0) {
            $descuentoAplicado = $totalSinDescuento * ($this->descuento / 100);
            $this->totalPago = $totalSinDescuento - $descuentoAplicado;
        } else {
            $this->totalPago = $totalSinDescuento;
        }*/
    }

    public function cerrarVenta()
    {
        $venta = $this->validate([
            'socio' => 'min:1',
            'productosTable' => 'min:1',
            'pagosTable' => 'min:1'
        ]);

        //dd($venta);

        //Calculamos total de los productos
        $montoTotalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
        //Calculamos total de los pagos
        $montoTotalPago = array_sum(array_column($this->pagosTable, 'monto_pago'));
        //Si los totales no coinciden, error.
        if ($montoTotalPago != $montoTotalVenta) {
            //Mandamos mensaje de sesion al alert
            session()->flash('fail', "El monto total de pago no es el correcto");
            return;
        }

        //
        if (!$this->invitado) {
            //Se crea la transaccion
            $this->registrarVenta($venta);
        }
    }

    private function registrarVenta($venta)
    {
        DB::transaction(function () use ($venta) {
            //Obtenemos la fecha-hora de cierre actual, con una instancia de Carbon.
            $fecha_cierre = now()->format('Y-m-d H:i:s');
            //Se calcular el total de los productos
            $total = array_sum(array_column($venta['productosTable'], 'subtotal'));
            //Concatenamos el nombre
            $nombre = $venta['socio']['nombre'] . ' ' . $venta['socio']['apellido_p'] . ' ' . $venta['socio']['apellido_m'];
            //Se registra la venta
            $ventaFinal = Venta::create([
                'id_socio' =>  $venta['socio']['id'],
                'nombre' => $nombre,
                'fecha_apertura' => $fecha_cierre,
                'fecha_cierre' => $fecha_cierre,
                'total' => $total,
                //'corte_caja' => $resultCaja[0]->corte,
                'clave_punto_venta' => 'RES'
            ]);
            //Detalles Venta
            foreach ($venta['productosTable'] as $key => $producto) {
                DetallesVentaProducto::create([
                    'folio_venta' => $ventaFinal->folio,
                    'codigo_catalogo' => $producto['codigo_catalogo'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'observaciones' => $producto['observaciones'],
                    'subtotal' => $producto['subtotal'],
                    'inicio' => '2024-12-12',
                ]);
            }
            //dd($venta['pagosTable']);
            //Detalles Pago
            foreach ($venta['pagosTable'] as $key => $pago) {
                $resultPago = DetallesVentaPago::create([
                    'folio_venta' => $ventaFinal->folio,
                    'id_socio' => $pago['id_socio'],
                    'nombre' => $pago['nombre'],
                    'monto' => $pago['monto_pago'],
                    'propina' => $pago['propina'],
                    'id_tipo_pago' => $pago['id_tipo_pago'],
                ]);
            }
        });
    }
}

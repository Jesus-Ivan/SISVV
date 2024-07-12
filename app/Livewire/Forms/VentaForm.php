<?php

namespace App\Livewire\Forms;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\PuntoVenta;
use App\Models\Socio;
use App\Models\SocioMembresia;
use App\Models\TipoPago;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaForm extends Form
{
    public $tipo_venta = "socio";    //El tipo de venta a realizar
    public $invitado;               //Invitado
    public $nombre_invitado;        //El nombre del invitado
    public $nombre_p_general;       //Nombre del cliente cuanso selecciona publico general
    public $nombre_empleado;        //Nombre del empleado
    public $socioSeleccionado;      //El socio seleccionado
    public $socio = [];             //Datos del socio

    public $socioPago;              //El socio seleccionado para agregar en metodo de pago
    public $id_pago;                //id del tipo de pago seleccionado en el modal
    public $monto_pago;             //el monto a pagar
    public $propina;                //Si dejo o no propina

    public $seachProduct = '';        //Input de busqueda de productos
    public $selected = [];            //Almacena los codigos de productos seleccionados del modal.

    public $productosTable = [];      //array de productos, que se muestran en la tabla (productos agregados)
    #[Locked]
    public $pagosTable = [];          //Array de pagos que se muestran en la tabla
    #[Locked]
    public $totalVenta = 0;           //El costo total de los articulos

    #[Locked]
    public $totalPago = 0;            //El total de los pagos
    public $totalPropina = 0;         //El total de la propina
    //public $descuento = 0;          //En caso de que se aplique un descuento a la cuenta
    //public $totalSinDescuento = 0;  //Centa total temporal en caso que se le aplique un descuento
    //public $totalConDescuento = 0;  //El total de la venta final

    public $permisospv;               //Almacena los permisos del usuario en el punto

    //REGLAS PARA VENTA AL SOCIO
    public $socioVentaRules = [
        'socio' => 'min:1',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1'
    ];

    //REGLAS PARA VENTA AL INVITADO
    public $invitadoVentaRules = [
        'socio' => 'min:1',
        'nombre_invitado' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1'
    ];

    //REGLAS PARA VENTA AL PUBLICO GENERAL
    public $generalVentaRules = [
        'nombre_p_general' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1'
    ];

    //REGLAS PARA VENTA AL EMPLEADO
    public $empleadoVentaRules = [
        'nombre_empleado' => 'required',
        'productosTable' => 'min:1',
        'pagosTable' => 'min:1'
    ];

    //RESETEAR VALORES DEL SELECT Y TABLA DE PAGOS
    public function resetVentas()
    {
        $this->reset(['socio', 'nombre_invitado', 'nombre_p_general', 'nombre_empleado']);
        $this->reset(['pagosTable', 'id_pago', 'monto_pago', 'socioPago', 'propina']);
    }

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

    public function incrementarProducto($productoIndex)
    {
        //Definimos una variable que apunta a la direccion de memoria del producto
        $articulo = &$this->productosTable[$productoIndex];
        //incrementamos en 1 la cantidad
        $articulo['cantidad']++;
        //Obtenemos el nuevo subtotal
        $articulo['subtotal'] = $articulo['cantidad'] * $articulo['precio'];
        $this->actualizarTotal();
    }

    public function decrementarProducto($productoIndex)
    {
        //Definimos una variable que apunta a la direccion de memoria del producto
        $articulo = &$this->productosTable[$productoIndex];
        //Comprobamos si la cantidad es positiva
        if ($articulo['cantidad'] > 1) {
            $articulo['cantidad']--;
            $articulo['subtotal'] = $articulo['cantidad'] * $articulo['precio'];
        }
        $this->actualizarTotal();
    }

    public function actualizarTotal()
    {
        //Se actualiza el total de los productos
        $this->totalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
    }

    //Recibe una instancia del modelo 'Socio' con el registro de la BD, para el registro del pago
    public function setSocioPago($socio)
    {
        //Validamos si el socio no esta con una membresia cancelada
        $resultMembresia = SocioMembresia::where('id_socio', $socio->id)->first();
        if (!$resultMembresia) {
            throw new Exception('No se encontro membresia registrada', 2);
        } else if ($resultMembresia->estado == 'CAN') {
            throw new Exception('Membresia de socio cancelada', 2);
        }
        $this->socioPago = $socio;
    }

    public function agregarPago()
    {
        //Validamos las entradas
        $reglas = [
            'id_pago' => 'required',
            'monto_pago' => 'required|numeric',
        ];

        if ($this->tipo_venta == 'socio' || $this->tipo_venta == 'invitado') {
            $reglas['socioPago'] = 'required';          // Agregar validacion de número de socio si es venta para socio
        }

        $validated = $this->validate($reglas);                                 //Validamos los atributos

        $this->validarFirma($validated);                                       //Validamos la firma
        $id_socio = $this->socioPago ? $this->socioPago->id : null; //Si es venta para socio, se obtiene el id del socio

        switch ($this->tipo_venta) {
            case 'socio':
                $nombre = $this->socioPago->nombre . ' ' . $this->socioPago->apellido_p . ' ' . $this->socioPago->apellido_m;
                break;
            case 'invitado':
                $nombre = $this->nombre_invitado;
                break;
            case 'empleado':
                $nombre = $this->nombre_empleado;
                break;
            case 'general':
                $nombre = $this->nombre_p_general;
                break;
            default:
                break;
        }

        //Se agrega el pago a la tabla de pagos
        $this->pagosTable[] = [
            'id_socio' => $id_socio,
            'nombre' => $nombre,
            'id_tipo_pago' => $this->id_pago,
            'descripcion_tipo_pago' => TipoPago::find($this->id_pago)->descripcion,
            'monto_pago' => $validated['monto_pago'],
            'propina' => $this->propina,
        ];
        //si es una venta diferente de invitado, limpiamos el socioPago
        if ($this->tipo_venta != 'invitado') {
            //Limpiar todos los campos del modal del pago
            $this->reset('socioPago');
        }
        //Continuamos limpiando el resto de propiedades del modal de pago
        $this->reset('id_pago', 'monto_pago', 'propina');

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
        $this->totalPago = array_sum(array_column($this->pagosTable, 'monto_pago'));

        //Aplicar descuento si es que existe
        /* if ($this->descuento > 0) {
            $descuentoAplicado = $totalSinDescuento * ($this->descuento / 100);
            $this->totalPago = $totalSinDescuento - $descuentoAplicado;
        } else {
            $this->totalPago = $totalSinDescuento;
        }*/
    }

    //Se ejecuta para cerrar una venta completa, si esta es nueva
    public function cerrarVentaNueva($codigopv)
    {
        //Validamos la informacion de la venta
        switch ($this->tipo_venta) {
            case 'socio':
                $venta = $this->validate($this->socioVentaRules);
                break;
            case 'invitado':
                $venta = $this->validate($this->invitadoVentaRules);
                break;
            case 'general':
                $venta = $this->validate($this->generalVentaRules);
                break;
            case 'empleado':
                $venta = $this->validate($this->empleadoVentaRules);
                break;
            default:
                break;
        }

        //Verificamos si el total de pago, es el mismo que el total de los productos
        $this->verificarMontos();

        //Variable auxiliar para almacenar el folio resultado de la venta
        $folioVenta = 0;
        //Se crea la transaccion
        DB::transaction(function () use ($venta, $codigopv, &$folioVenta) {
            //Crear la venta y guardamos el resultado de la insersion en la BD, en la variable 'resultVenta'
            $resultVenta = $this->registrarVenta($venta, $codigopv, true, $this->tipo_venta);

            //Obtenemos el folio
            $folioVenta = $resultVenta->folio;

            //crear los detalles de los productos
            $this->registrarProductosVenta($folioVenta, $venta);
            //Crear los detalles de los pagos
            $this->registrarPagosVenta($folioVenta, $venta, $codigopv);
        }, 2);
        //Limpiamos atributos
        $this->limpiarComponente();
        //Devolvemos objeto del resultado al componente
        return $folioVenta;
    }

    //Se ejecuta para guardar una venta, cuando es nueva
    public function guardarVentaNueva($codigopv)
    {
        //Validamos la informacion de la venta
        switch ($this->tipo_venta) {
            case 'socio':
                $venta = $this->validate([
                    'socio' => 'min:1',
                    'productosTable' => 'min:1',
                ]);
                break;
            case 'invitado':
                $venta = $this->validate([
                    'socio' => 'min:1',
                    'nombre_invitado' => 'required',
                    'productosTable' => 'min:1',
                ]);
                break;
            case 'general':
                $venta = $this->validate([
                    'nombre_p_general' => 'required',
                    'productosTable' => 'min:1',
                ]);
                break;
            case 'empleado':
                $venta = $this->validate([
                    'nombre_empleado' => 'required',
                    'productosTable' => 'min:1',
                ]);
                break;
            default:
                break;
        }


        $tipo_venta = $this->tipo_venta;

        //Se crea la transaccion
        DB::transaction(function () use ($venta, $codigopv, $tipo_venta) {
            //Crear la venta y guardamos el resultado de la insersion en la BD.
            $resultVenta = $this->registrarVenta($venta, $codigopv, false, tipo_venta: $tipo_venta);
            //crear los detalles de los productos
            $this->registrarProductosVenta($resultVenta->folio, $venta);
        }, 2);
        $this->limpiarComponente();
    }

    //Actualizar una venta existente (actualiza la tabla de productos y total de la venta)
    public function guardarVentaExistente($folio)
    {
        //Verificamos si la venta no esta cerrada
        $this->validarVentaCerrada($folio);
        //Calculamos el nuevo total de la venta
        $total = array_sum(array_column($this->productosTable, 'subtotal'));
        //Creamos una fecha de inicio para los detalles de los productos que se van a guardar
        $inicio = now()->format('Y-m-d H:i:s');

        DB::transaction(function () use ($folio, $total, $inicio) {
            //Recorremos todos los items de la tabla
            foreach ($this->productosTable as $key => $producto) {
                //Verificamos si el item que se itera, cuenta con un 'id' de la base de datos
                if (array_key_exists('id', $producto)) {
                    //Actualizar el registro en la BD
                    DetallesVentaProducto::where('id', $producto['id'])
                        ->update(
                            ['cantidad' => $producto['cantidad'], 'subtotal' => $producto['subtotal']]
                        );
                } else {
                    //Crear el nuevo item
                    DetallesVentaProducto::create([
                        'folio_venta' => $folio,
                        'codigo_catalogo' => $producto['codigo_catalogo'],
                        'cantidad' => $producto['cantidad'],
                        'precio' => $producto['precio'],
                        'observaciones' => $producto['observaciones'],
                        'subtotal' => $producto['subtotal'],
                        'inicio' => $inicio,
                        'tiempo' => $producto['tiempo'],
                    ]);
                }
            }
            //Actualizamos el total de la venta 
            Venta::where('folio', $folio)->update(['total' => $total]);
        }, 2);
    }

    //Cerrar una venta existente (actualiza toda la venta)
    public function cerrarVentaExistente($folio, $codigopv)
    {
        //Verificamos si la venta no esta cerrada
        $this->validarVentaCerrada($folio);
        //Validamos que de la venta tenga metodos de pago y productos
        $venta = $this->validate([
            'productosTable' => 'min:1',
            'pagosTable' => 'min:1'
        ]);
        //Verificamos si el total de pago, es el mismo que el total de los productos
        $this->verificarMontos();

        DB::transaction(function () use ($folio, $venta, $codigopv) {
            //Guardamos los metodos de pago
            $this->registrarPagosVenta($folio, $venta, $codigopv);
            //Guardamos los cambios de la tabla de productos
            $this->guardarVentaExistente($folio);
            //Cerramos la venta con la fecha actual
            Venta::where('folio', $folio)->update(['fecha_cierre' => now()->format('Y-m-d H:i:s')]);
        }, 2);
    }

    //Esta funcion registra la venta en la tabla "ventas"
    private function registrarVenta($venta, $codigopv, $isClosed = false, $tipo_venta)
    {
        //Buscamos cajas disponibles en el punto de venta actual
        $resultCaja = $this->buscarCaja();
        //Obtenemos la fecha-hora de actual, con una instancia de Carbon.
        $fecha_cierre = now()->format('Y-m-d H:i:s');
        //Se calcula el total de los productos
        $total = array_sum(array_column($venta['productosTable'], 'subtotal'));

        //Si el socio tiene un id, lo concatenamos al nombre
        //$nombre = isset($venta['socio']['id']) ? $venta['socio']['nombre'] . ' ' . $venta['socio']['apellido_p'] . ' ' . $venta['socio']['apellido_m'] : null;

        switch ($this->tipo_venta) {
            case 'socio':
                $nombre = $venta['socio']['nombre'] . ' ' . $venta['socio']['apellido_p'] . ' ' . $venta['socio']['apellido_m'];
                break;
            case 'invitado':
                $nombre = $venta['nombre_invitado'];
                break;
            case 'general':
                $nombre = $venta['nombre_p_general'];
                break;
            case 'empleado':
                $nombre = $venta['nombre_empleado'];
                break;
            default:
                $nombre = null;
        }

        //Se registra la venta
        return Venta::create([
            'tipo_venta' => $tipo_venta,
            'id_socio' =>  $venta['socio']['id'] ?? null,
            'nombre' => $nombre,
            'fecha_apertura' => $fecha_cierre,
            'fecha_cierre' => $isClosed ? $fecha_cierre : null,
            'total' => $total,
            'corte_caja' => $resultCaja->corte,
            'clave_punto_venta' => $codigopv
        ]);
    }

    //Registra los detalles de los productos en la tabla "detalles_ventas_productos"
    private function registrarProductosVenta($folio, $venta)
    {
        $inicio = now()->format('Y-m-d H:i:s');
        //Detalles Venta
        foreach ($venta['productosTable'] as $key => $producto) {
            DetallesVentaProducto::create([
                'folio_venta' => $folio,
                'codigo_catalogo' => $producto['codigo_catalogo'],
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
                'observaciones' => $producto['observaciones'],
                'subtotal' => $producto['subtotal'],
                'inicio' => $inicio,
                'tiempo' => $producto['tiempo'],
            ]);
        }
    }

    //Registra los detalles de los productos en la tabla "detalles_ventas_pagos"
    private function registrarPagosVenta($folio, $venta, $codigopv)
    {
        //Buscamos el punto de venta.
        $puntoVenta = PuntoVenta::find($codigopv);
        //Detalles de Pago
        foreach ($venta['pagosTable'] as $key => $pago) {
            $resultPago = DetallesVentaPago::create([
                'folio_venta' => $folio,
                'id_socio' => $pago['id_socio'],
                'nombre' => $pago['nombre'],
                'monto' => $pago['monto_pago'],
                'propina' => $pago['propina'],
                'id_tipo_pago' => $pago['id_tipo_pago'],
            ]);

            //Verificamos que el tipo de venta sea socio o invitado de socio (para cargarlo al estado de cuenta)
            if ($this->tipo_venta == 'socio' ||  $this->tipo_venta == 'invitado') {
                //Verificamos si el tipo de pago es firma 
                if (strcasecmp('FIRMA', $pago['descripcion_tipo_pago']) == 0) {
                    //Creamos el concepto en el estado de cuenta (con abono 0)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $folio . ' - ' . $puntoVenta->nombre,
                        'fecha' => now()->toDateString(),
                        'cargo' => $pago['monto_pago'],
                        'saldo' => $pago['monto_pago'],
                        'consumo' => true
                    ]);
                } else {
                    //Creamos el concepto en el estado de cuenta (con abono total)
                    EstadoCuenta::create([
                        'id_socio' => $pago['id_socio'],
                        'id_venta_pago' => $resultPago->id,
                        'concepto' => 'NOTA VENTA: ' . $folio . ' - ' . $puntoVenta->nombre,
                        'fecha' => now()->toDateString(),
                        'cargo' => $pago['monto_pago'],
                        'abono' => $pago['monto_pago'],
                        'saldo' => 0,
                        'consumo' => true
                    ]);
                }
            }
        }
    }

    private function validarFirma($validated)
    {
        //Verificamos el tipo de venta sea a un socio o invitado del mismo
        if ($this->tipo_venta == 'socio' || $this->tipo_venta == 'invitado') {
            //Verificar si el metodo actual seleccionado es firma.
            $tipo_pago = TipoPago::where('id', $validated['id_pago'])
                ->where('descripcion', 'like', 'FIRMA')
                ->first();
            //Si la consulta devuelve valor diferente de null
            if ($tipo_pago) {
                //Buscamos el socio
                $result = Socio::find($validated['socioPago']['id']);
                //Si el socio no tiene firma
                if (!$result->firma) {
                    throw new Exception("Este socio no tiene firma autorizada", 1);
                }
            }
        }
    }

    private function validarVentaCerrada($folioVenta)
    {
        //Buscamos la venta
        $result = Venta::find($folioVenta);
        //Si la venta tiene fecha de cierre
        if ($result->fecha_cierre) {
            throw new Exception("Esta venta esta cerrada", 1);
        }
    }

    private function buscarCaja()
    {
        //Buscamos caja abrierta en el punto actual, en el dia actual
        $result = Caja::where('clave_punto_venta', $this->permisospv->clave_punto_venta)
            ->whereDate('fecha_apertura', now()->toDateString())
            ->whereNull('fecha_cierre')
            ->first();
        //Si no hay caja
        if (!$result) {
            throw new Exception("No hay caja abierta para este punto de venta");
        }
        return  $result;
    }

    private function verificarMontos()
    {
        //Calculamos total de los productos
        $montoTotalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
        //Calculamos total de los pagos
        $montoTotalPago = array_sum(array_column($this->pagosTable, 'monto_pago'));
        //Si los totales no coinciden, error.
        if ($montoTotalPago != $montoTotalVenta) {
            //Mandamos mensaje de sesion al alert
            throw new Exception("El monto total de pago no es el correcto");
        }
    }

    private function limpiarComponente()
    {
        $this->reset(
            'tipo_venta',
            'invitado',
            'nombre_invitado',
            'nombre_empleado',
            'socioSeleccionado',
            'socio',
            'socioPago',
            'id_pago',
            'monto_pago',
            'propina',
            'seachProduct',
            'selected',
            'productosTable',
            'pagosTable',
            'totalVenta',
            'totalPago',
            'totalPropina'
        );
    }
}

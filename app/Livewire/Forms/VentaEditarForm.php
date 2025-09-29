<?php

namespace App\Livewire\Forms;

use App\Models\Caja;
use App\Models\CorreccionVenta;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\Venta;
use Exception;
use Livewire\Form;

class VentaEditarForm extends Form
{
    //Propiedades almacenar los originales
    public $venta, $productos, $pagos;

    /**
     * Guarda los valores originales de la venta en el formulario
     */
    public function setOriginal($venta_original, $productos_original, $pagos_original)
    {
        $this->venta = $venta_original;
        $this->productos = $productos_original;
        $this->pagos = $pagos_original;
    }

    /**
     * Esta funcion convierte en cortesia la venta.
     */
    public function cortesia($folio_seleccionado, $observaciones)
    {
        $result = Venta::find($folio_seleccionado);    //Buscar datos generales, en la tabla 'ventas'
        if (!$result) {
            //Si no hay registros, lanzar error
            throw new Exception("No se encontro la venta " . $folio_seleccionado);
        }
        //Buscar los productos de la venta
        $detalles_productos = DetallesVentaProducto::where('folio_venta', $folio_seleccionado)
            ->get();
        //Buscar los detalles de pago
        $detalles_pagos = DetallesVentaPago::where('folio_venta', $folio_seleccionado)
            ->get();

        //Actualizar el tipo de venta, total y observaciones
        $result->tipo_venta = 'cortesia';
        $result->total = 0;
        $result->observaciones = $observaciones;
        $result->save();
        //Actualizar los productos
        foreach ($detalles_productos as $key => $producto) {
            $producto->subtotal = 0;
            $producto->save();
        }
        //Actualizar el metodo de pago
        foreach ($detalles_pagos as $key => $pago) {
            $pago->id_tipo_pago = 1;
            $pago->monto = 0;
            $pago->save();
        }
        //Comprobar si la venta tiene id_socio
        if ($result->id_socio) {
            //Obtenemos los ID de los pagos de la venta, en la tabla 'detalles_ventas_pagos'
            $id_pagos = array_column($detalles_pagos->toArray(), 'id');
            //Buscar los registros correspondientes de pagos de la venta, en el estado de cuenta
            $estado_cuenta = EstadoCuenta::whereIn('id_venta_pago', $id_pagos)->get();
            //Actualizamos el estado de cuenta, para la cortesia
            foreach ($estado_cuenta as $key => $row) {
                $row->cargo = 0;
                $row->abono = 0;
                $row->saldo = 0;
                $row->save();
            }
        }
    }

    /**
     * Registra la correccion en la tabla correspondiente 'correcciones_ventas'
     */
    public function registrarCorreccion(array $venta, $solicitante_id,  $motivo_id)
    {
        //Buscar al usuario que solicita la correccion
        $solicitante = User::find($solicitante_id);
        //Crear el registro
        CorreccionVenta::create([
            'user_name' => auth()->user()->name,
            'folio_venta' => $venta['folio'],
            'tipo_venta' => $venta['tipo_venta'],
            'solicitante_name' => $solicitante->name,
            'id_motivo' => $motivo_id,
            'corte_caja' => $venta['corte_caja']
        ]);
    }

    /**
     * Si el parametro 'venta_editada' y la propiedad 'venta', son diferentes actualiza el punto de venta y el corte de caja.
     */
    public function actualizarPunto(array $venta_editada)
    {
        //Verificar si el corte de caja pertenece al punto dado
        $caja = Caja::with('puntoVenta')->where([
            ['corte', '=', $venta_editada['corte_caja']],
            ['clave_punto_venta', '=', $venta_editada['clave_punto_venta']],
        ])->first();

        if (! $caja) {
            throw new Exception("No existe caja: " . $venta_editada['corte_caja'] . ", punto: " . $venta_editada['clave_punto_venta']);
        }

        //Si los arrays , tienen diferencias entre si
        if (count(array_diff_assoc($this->venta, $venta_editada)) != 0) {
            //Actualizar el punto de venta
            Venta::where('folio', $venta_editada['folio'])
                ->update(
                    [
                        'corte_caja' => $venta_editada['corte_caja'],
                        'clave_punto_venta' => $venta_editada['clave_punto_venta']
                    ]
                );
            //Buscar los detalles de pago
            $detalles_pago = DetallesVentaPago::where('folio_venta', $venta_editada['folio'])
                ->get()->toArray();

            //Actualizar el concepto en el estado de cuenta
            EstadoCuenta::whereIn('id_venta_pago', array_column($detalles_pago, 'id'))->update([
                'concepto' => 'NOTA VENTA: ' . $venta_editada['folio'] . ' - ' . $caja->puntoVenta->nombre
            ]);
        }
    }

    /**
     * Actualiza al socio titular del ticket de venta, si el parametro 'nuevo_socio' es null no ejecuta ningun cambio
     */
    public function actualizarSocioTitular(array $venta_editada, $nuevo_socio)
    {
        //Si el nuevo socio esta definido
        if ($nuevo_socio) {
            //verificar el tipo de venta
            if ($venta_editada['tipo_venta'] == 'socio') {
                //Actualizar el nombre y el id_socio
                Venta::where('folio', $venta_editada['folio'])->update([
                    'id_socio' => $nuevo_socio['id'],
                    'nombre' => $nuevo_socio['nombre'] . ' ' . $nuevo_socio['apellido_p'] . ' ' . $nuevo_socio['apellido_m']
                ]);
            } else if ($venta_editada['tipo_venta'] == 'invitado') {
                //Actualizar solo el nombre del socio.
                Venta::where('folio', $venta_editada['folio'])->update(['id_socio' => $nuevo_socio['id']]);
            }
        }
    }

    /**
     * Actualiza los productos vendidos
     */
    public function actualizarProductos(array $productos_editados, $solicitante_id)
    {
        $solicitante = User::find($solicitante_id);
        foreach ($productos_editados as $key => $producto) {
            if (array_key_exists('deleted', $producto)) {
                //Actualizar el registro en la BD (eliminar)
                DetallesVentaProducto::where('id', $producto['id'])
                    ->update([
                        'id_cancelacion' => $producto['id_cancelacion'],
                        'motivo_cancelacion' => $producto['motivo_cancelacion'],
                        'deleted_at' => now(),
                        'usuario_cancela' => $solicitante->name
                    ]);
            } else {
                //Revisar si hubo cambio entre los subtotales
                if ($producto['subtotal'] <> $this->productos[$key]['subtotal']) {
                    //Actualizar el producto
                    DetallesVentaProducto::where([
                        ['folio_venta', '=', $producto['folio_venta']],
                        ['id', '=', $producto['id']]
                    ])
                        ->update(['subtotal' => $producto['subtotal']]);
                }
            }
        }
    }

    /**
     * Actualiza los pagos, si hubo alguna modificacion
     */
    public function actualizarPagos(array $pagos_editados)
    {
        $firma = TipoPago::where("descripcion", 'like', '%FIRMA%')->first();
        foreach ($pagos_editados as $key => $pago) {
            //Si el pago tiene la clave 'deleted'
            if (array_key_exists('deleted', $pago)) {
                //Borrar el registro de la tabla 'detalles_ventas_pagos'
                DetallesVentaPago::destroy($pago['id']);
                //Si la venta es de tipo socio o invitado del socio
                if ($this->venta['tipo_venta'] == 'socio' || $this->venta['tipo_venta'] == 'invitado') {
                    //Borrar pago del estado de cuenta
                    EstadoCuenta::where('id_venta_pago', $pago['id'])->delete();
                }
            } else {
                //Calcular la diferencia entre el array
                $diff = array_diff_assoc($pago, $this->pagos[$key]);
                //Si hubo diferencias
                if (count($diff) > 0) {
                    //Actualizar el pago en la BD
                    DetallesVentaPago::where('id', $pago['id'])->update($diff);
                    //Si existe la clave 'id_socio' en la diferencia (significa que se modifico)
                    if (array_key_exists('id_socio', $diff)) {
                        //Mover el pago del estado de cuenta del socio.
                        EstadoCuenta::where('id_venta_pago', $pago['id'])->update([
                            'id_socio' => $diff['id_socio'],
                        ]);
                    }

                    //Si existe la clave 'monto' en la diferencia de arrays actualizar el estado de cuenta
                    if (array_key_exists('monto', $diff)) {
                        EstadoCuenta::where('id_venta_pago', $pago['id'])->update([
                            'cargo' => $diff['monto'],
                            'abono' => $diff['monto']
                        ]);
                    }

                    if (array_key_exists('id_tipo_pago', $diff)) {
                        //Obtenemos el id de metodo de pago original
                        $original_id = $this->pagos[$key]['id_tipo_pago'];
                        //Guardamos el nuevo id del metodo de pago
                        $nuevo_id = $diff['id_tipo_pago'];
                        //Verificamos si el id original era de firma
                        if ($original_id == $firma->id) {
                            throw new Exception(
                                "No se puede cambiar el pago de: "
                                    . $pago['nombre']
                                    . ', de : '
                                    . $firma->descripcion
                                    . ', al nuevo metodo de pago'
                            );
                        }
                        //Si el nuevo id es de firma
                        if ($nuevo_id == $firma->id) {
                            if ($this->venta['tipo_venta'] == 'empleado' || $this->venta['tipo_venta'] == 'general')
                                throw new Exception('Metodo de pago, "FIRMA" no valido para el tipo de venta');
                            //Actualizar el estado de cuenta
                            EstadoCuenta::where('id_venta_pago', $pago['id'])->update([
                                'cargo' => array_key_exists('monto', $diff) ? $diff['monto'] : $pago['monto'],
                                'abono' => 0,
                                'saldo' => array_key_exists('monto', $diff) ? $diff['monto'] : $pago['monto']
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Si el parametro 'nuevo_total' es diferente al total de la propiedad, actualiza el total de la venta en la tabla 'ventas'
     */
    public function actualizarTotal($nuevo_total)
    {
        if ($nuevo_total != $this->venta['total']) {
            //Actualizar el valor en la BD
            Venta::where('folio', $this->venta['folio'])->update(['total' => $nuevo_total]);
        }
    }

    /**
     * Elimina la nota de las tablas correspondientes, excepto aquellas que contienen firma
     */
    public function eliminarNota(array $venta)
    {
        //Bucar metodo de pago de firma
        $firma = TipoPago::where('descripcion', 'like', '%FIRMA%')->first();

        //Buscar los detalles del pago
        $pagos_result = DetallesVentaPago::where('folio_venta', $venta['folio'])->get()->toArray();

        //Filtrar los pagos con firma
        $firmas_pago = array_filter($pagos_result, function ($pagoItem) use ($firma) {
            return $pagoItem['id_tipo_pago'] == $firma->id;
        });

        if (count($firmas_pago)) {
            throw new Exception('No se puede eliminar la nota de la venta, ya que contiene firma');
        } else {
            //Eliminar la nota 
            Venta::destroy($venta['folio']);
            //Buscar los productos
            $result_productos = DetallesVentaProducto::where('folio_venta', $venta['folio'])
                ->get()->toArray();
            //Buscar los pagos
            $result_pagos = DetallesVentaPago::where('folio_venta', $venta['folio'])
                ->get()->toArray();
            //Eliminar los detalles de la compra
            DetallesVentaProducto::destroy(array_column($result_productos, 'id'));
            DetallesVentaPago::destroy(array_column($result_pagos, 'id'));
            //Si la venta es de tipo socio o invitado
            if ($venta['tipo_venta'] == 'socio' || $venta['tipo_venta'] == 'invitado') {
                //Eliminar la venta del socio de su estado de cuenta
                EstadoCuenta::whereIn('id_venta_pago', array_column($result_pagos, 'id'))
                    ->delete();
            };
        }
    }
}

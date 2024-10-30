<?php

namespace App\Livewire\Forms;

use App\Models\CorreccionVenta;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\User;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaEditarForm extends Form
{

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
        ]);
    }

    public function actualizarPunto(array $venta, $clave_punto, $corte_caja){
        
    }
}

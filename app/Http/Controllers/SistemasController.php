<?php

namespace App\Http\Controllers;

use App\Constants\PuntosConstants;
use App\Exports\ProductosVendExport;
use App\Models\Caja;
use App\Models\DetallesCaja;
use App\Models\Proveedor;
use App\Models\PuntoVenta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SistemasController extends Controller
{
    public function prodVendidos()
    {
        return view('sistemas.Puntos.rep-prod-vendidos', ['puntos' => PuntoVenta::all()]);
    }

    public function repEntradas()
    {
        return view('sistemas.Almacen.reporte-entradas', ['proveedores' => Proveedor::all()]);
    }

    /**
     * Responde a la ruta, para obtener el reporte de productos vendidos
     */
    public function getReporteVendidos(Request $request)
    {
        $codigopv = $request->input('codigopv');    //Obtenemos de la peticion lo parametros
        $fInicio = $request->input('fInicio');
        $fFin = $request->input('fFin');
        $eliminados = $request->input('eliminados');
        //Definir variable de productos eliminados (opcional)
        $prod_eliminados = null;

        //Validamos las entradas
        $request->validate([
            'codigopv' => ['required'],
            'fInicio' => ['required'],
            'fFin' => ['required']
        ]);
        //Buscamos TODAS las cajas que coincidan entre las fechas deseadas.
        $cajas = Caja::whereDate('fecha_apertura', '>=', $fInicio)
            ->whereDate('fecha_apertura', '<=', $fFin)
            ->get()
            ->toArray();
        /**
         * Ventas sin productos eliminados
         */
        if ($codigopv == 'ALL') {
            //Buscar las ventas de todos los puntos EN BASE A LOS CORTES DE CAJA
            $ventas = Venta::with(['detallesProductos', 'puntoVenta'])
                ->whereIn('corte_caja', array_column($cajas, 'corte'))
                ->get();
        } else {
            //Solo la venta de un punto especifico
            $ventas = Venta::with(['detallesProductos', 'puntoVenta'])
                ->whereIn('corte_caja', array_column($cajas, 'corte'))
                ->where('clave_punto_venta', $codigopv)
                ->get();
        }
        /**
         * Ventas con productos eliminados
         */
        if ($eliminados) {
            if ($codigopv == 'ALL') {
                $prod_eliminados = Venta::with([
                    'detallesProductos' => function ($query) {
                        $query->onlyTrashed();
                    },
                    'puntoVenta',
                ])
                    ->whereIn('corte_caja', array_column($cajas, 'corte'))
                    ->get();
            } else {
                //Solo la venta de un punto especifico
                $prod_eliminados = Venta::with([
                    'detallesProductos' => function ($query) {
                        $query->onlyTrashed();
                    },
                    'puntoVenta',
                ])
                    ->whereIn('corte_caja', array_column($cajas, 'corte'))
                    ->where('clave_punto_venta', $codigopv)
                    ->get();
            }
        }

        //Devolvemos el excel
        return Excel::download(
            new ProductosVendExport($ventas->toArray(), $prod_eliminados),
            'Productos vendidos ' . $codigopv . ' - ' . $fInicio . ' - ' . $fFin . '.xlsx'
        );
    }

    public function editarVenta(Request $request)
    {
        //Obtener el folio de la url
        $folioVenta = $request->segment(4);
        return view('sistemas.Puntos.notas-editar', [
            'folioVenta' => $folioVenta
        ]);
        return "editando";
    }

    /**
     * Se encarga de llenar la tabla 'detalles_caja', por primera vez.
     * En base a las ventas realizadas
     */
    public function detallesCaja(Request $request)
    {
        $ventasPagos = DB::table('detalles_ventas_pagos')
            ->join('ventas', 'detalles_ventas_pagos.folio_venta', '=', 'ventas.folio')
            ->select('detalles_ventas_pagos.*', 'ventas.corte_caja', 'ventas.fecha_apertura')
            ->get();

        DB::transaction(function () use ($ventasPagos) {
            foreach ($ventasPagos as $key => $pago) {
                DetallesCaja::create([
                    'corte_caja' => $pago->corte_caja,
                    'folio_venta' => $pago->folio_venta,
                    'id_socio' => $pago->id_socio,
                    'nombre' => $pago->nombre,
                    'monto' => $pago->monto,
                    'propina' => $pago->propina,
                    'tipo_movimiento' => PuntosConstants::INGRESO_KEY,
                    'id_tipo_pago' => $pago->id_tipo_pago,
                    'fecha_venta' => $pago->fecha_apertura,
                ]);
            }
        });
        return 'Creo que funciono xd';
    }
}

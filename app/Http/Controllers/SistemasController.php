<?php

namespace App\Http\Controllers;

use App\Exports\ProductosVendExport;
use App\Models\Caja;
use App\Models\Proveedor;
use App\Models\PuntoVenta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
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

    //Responde a la ruta, para obtener el reporte de productos vendidos
    public function getReporteVendidos(Request $request)
    {
        $codigopv = $request->input('codigopv');    //Obtenemos de la peticion lo parametros
        $fInicio = $request->input('fInicio');
        $fFin = $request->input('fFin');

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
        if ($codigopv == 'ALL') {
            //Buscar las ventas de todos los puntos EN BASE A LOS CORTES DE CAJA
            $ventas = Venta::with('puntoVenta')
                ->whereIn('corte_caja', array_column($cajas, 'corte'))
                ->get();
        } else {
            //Solo la venta de un punto especifico
            $ventas = Venta::with('puntoVenta')
                ->whereIn('corte_caja', array_column($cajas, 'corte'))
                ->where('clave_punto_venta', $codigopv)
                ->get();
        }
        //Devolvemos el excel
        return Excel::download(
            new ProductosVendExport($ventas->toArray()),
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
}

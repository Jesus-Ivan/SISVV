<?php

namespace App\Http\Controllers;

use App\Exports\ProductosVendExport;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SistemasController extends Controller
{
    public function prodVendidos()
    {
        return view('sistemas.Puntos.rep-prod-vendidos', ['puntos' => PuntoVenta::all()]);
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

        if ($codigopv == 'ALL') {
            //Buscar las ventas de todos los puntos
            $ventas = Venta::with('puntoVenta')
                ->whereDate('fecha_apertura', '>=', $fInicio)
                ->whereDate('fecha_apertura', '<=', $fFin)
                ->get();
        } else {
            //Solo la venta de un punto especifico
            $ventas = Venta::with('puntoVenta')
                ->whereDate('fecha_apertura', '>=', $fInicio)
                ->whereDate('fecha_apertura', '<=', $fFin)
                ->where('clave_punto_venta', $codigopv)
                ->get();
        }
        //Devolvemos el excel
        return Excel::download(
            new ProductosVendExport($ventas->toArray()),
            'Productos vendidos ' . $codigopv . ' - ' . $fInicio . ' - ' . $fFin . '.xlsx'
        );
    }
}

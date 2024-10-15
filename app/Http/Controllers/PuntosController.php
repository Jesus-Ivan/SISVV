<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CambioTurno;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;


class PuntosController extends Controller
{
    public function index(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        return view('puntos.index', ['codigopv' => $codigopv, 'permisospv' => $permisospv]);
    }


    public function ventasIndex(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        $puntoVenta = PuntoVenta::find($codigopv);
        if (!$puntoVenta) {
            return "No se encontro el punto de venta";
        }
        return view(
            'puntos.Ventas.ventas',
            [
                'codigopv' => $codigopv,
                'nombrepv' => $puntoVenta->nombre,
                'permisospv' => $permisospv
            ]
        );
    }

    public function nuevaVenta(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        $puntoVenta = PuntoVenta::find($codigopv);
        if (!$puntoVenta) {
            return "No se encontro el punto de venta";
        }
        return view(
            'puntos.Ventas.nueva-venta',
            [
                'codigopv' => $codigopv,
                'nombrepv' => $puntoVenta->nombre,
                'permisospv' => $permisospv
            ]
        );
    }

    public function editarVenta(Request $request)
    {
        $permisospv = $request->get('permisos_pv');
        $codigopv = $request->segment(2);
        $folioVenta = $request->segment(5);
        //Obtenemos la venta
        $venta = Venta::find($folioVenta);
        //Si la venta no esta cerrada
        if (!$venta->fecha_cierre) {
            return view('puntos.Ventas.editar-venta', [
                'codigopv' => $codigopv,
                'permisospv' => $permisospv,
                'venta' => $venta
            ]);
        } else {
            return redirect()->route('pv.ventas', ['codigopv' => $codigopv]);
        }
    }

    public function verVenta(Request $request)
    {
        $permisospv = $request->get('permisos_pv');
        $codigopv = $request->segment(2);
        $folioVenta = $request->segment(5);
        return view('puntos.Ventas.ver-venta', [
            'codigopv' => $codigopv,
            'permisospv' => $permisospv,
            'folioventa' => $folioVenta
        ]);
    }

    public function reporteVentas(Request $request)
    {
        $permisospv = $request->get('permisos_pv');
        $codigopv = $request->segment(2);
        $puntoVenta = PuntoVenta::find($codigopv);
        return view('puntos.Ventas.reporte-ventas', [
            'codigopv' => $codigopv,
            'nombrepv' => $puntoVenta->nombre,
            'permisospv' => $permisospv,
        ]);
    }

    public function verSocios(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        return view('puntos.Socios.socios', ['codigopv' => $codigopv, 'permisospv' => $permisospv]);
    }

    public function caja(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        return view('puntos.caja.caja', ['codigopv' => $codigopv, 'permisospv' => $permisospv]);
    }

    public function prodVendidos(Request $request)
    {
        $permisospv = $request->get('permisos_pv'); //Obtenemos los permisos incrutados en la peticion
        $codigopv = $request->segment(2); //'codigopv' está en el segundo segmento de la ruta
        return view('puntos.Inventario.rep-prod-vendidos', [
            'puntos' => PuntoVenta::all(),
            'codigopv' => $codigopv,
            'permisospv' => $permisospv
        ]);
    }
}

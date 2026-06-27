<?php

namespace App\Http\Controllers;

use App\Constants\PuntosConstants;
use App\Exports\ProductosVendExport;
use App\Exports\ProdVendTotalExport;
use App\Models\Caja;
use App\Models\Socio;
use App\Models\DetallesCaja;
use App\Models\DetallesVentaProducto;
use App\Models\Producto;
use App\Models\ProductoZona;
use App\Models\Proveedor;
use App\Models\PuntoVenta;
use App\Models\User;
use App\Models\Venta;
use App\Models\ZonaImpresion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SistemasController extends Controller
{
    public function editarCuotas(Socio $socio)
    {
        return view('sistemas.Recepcion.editar-cuotas', ['socio' => $socio]);
    }

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

    /**
     * Obtiene el total de los productos vendidos\
     * En todos los puntos de venta
     */
    public function reporteVendidosTotal(Request $request)
    {
        $fInicio = $request->input('fInicio');
        $fFin = $request->input('fFin');
        $legacy = $request->input('legacy');
        //Validamos las entradas
        $request->validate([
            'fInicio' => ['required'],
            'fFin' => ['required']
        ]);

        //Buscamos TODAS las cajas que coincidan entre las fechas deseadas.
        $cajas = Caja::whereDate('fecha_apertura', '>=', $fInicio)
            ->whereDate('fecha_apertura', '<=', $fFin)
            ->get();
        //Agrupamos por punto de venta
        $cajas_agrupadas = $cajas->groupBy('clave_punto_venta')->toArray();

        //definimos array auxiliar
        $data = [];

        //Si la opcion legacy estaba marcada
        if ($legacy) {
            //Para cada grupo de cajas (correspondinte a las cajas en el rango de fechas.)
            foreach ($cajas_agrupadas as $clave_punto => $rows) {
                //Total de productos vendidos(sumados por clave), segun las ventas en un grupo de cajas.
                $totalesPorProducto = DetallesVentaProducto::with('catalogoProductos')
                    ->whereHas('venta', function ($query) use ($rows) {
                        $query->whereIn('corte_caja', array_column($rows, 'corte'));
                    })
                    ->select('codigo_catalogo')
                    ->selectRaw('SUM(cantidad) as total_vendido')
                    ->groupBy('codigo_catalogo')
                    ->get()
                    ->toArray();

                $data[$clave_punto] = $totalesPorProducto;
            }
        } else {
            //Para cada grupo de cajas (correspondinte a las cajas en el rango de fechas.)
            foreach ($cajas_agrupadas as $clave_punto => $rows) {
                //Total de productos vendidos(sumados por clave), segun las ventas en un grupo de cajas.
                $totalesPorProducto = DetallesVentaProducto::with('productos')
                    ->whereHas('venta', function ($query) use ($rows) {
                        $query->whereIn('corte_caja', array_column($rows, 'corte'));
                    })
                    ->select('clave_producto')
                    ->selectRaw('SUM(cantidad) as total_vendido')
                    ->groupBy('clave_producto')
                    ->get()
                    ->toArray();

                $data[$clave_punto] = $totalesPorProducto;
            }
        }
        //Devolvemos el excel
        return Excel::download(
            new ProdVendTotalExport($data),
            'Productos Vend.Total ' . $fInicio . ' - ' . $fFin . '.xlsx'
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
     * Crea la configuracion inicial de la tabla 'productos_zonas_impresion'\
     * Para imprimir los productos en la primera zona registrada
     */
    public function crearZonasImpresion(Request $request)
    {
        //Obtener todos la primera zona de impresion
        $first = ZonaImpresion::find(1);
        //Obtener los puntos de venta
        $puntos = PuntoVenta::where('inventariable', true)
            ->get();

        if ($first) {
            //Realizar la asignacion
            DB::transaction(function () use ($first, $puntos) {
                //Obtener todos los porductos con la marca
                $productos = Producto::where('print_default', 1)
                    ->get();
                foreach ($productos as $key => $producto) {
                    foreach ($puntos as $key_punto => $punto) {
                        ProductoZona::create([
                            'clave_producto' => $producto->clave,
                            'clave_punto' => $punto->clave,
                            'id_zona' => $first->id
                        ]);
                    }
                }
            });
            return 'EXITO :D! generando la tabla: zonas_impresion';
        }
        return 'No hay zonas de impresion en la tabla: zonas_impresion';
    }
}

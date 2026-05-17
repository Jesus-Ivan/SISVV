<?php

namespace App\Http\Controllers;

use App\Exports\ComandasExport;
use App\Models\Caja;
use App\Models\DetallesVentaProducto;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CocinaController extends Controller
{
    /**
     * Prepara la vista para obtener el reporte de comandas
     */
    public function vistaOrdenes()
    {
        $hoy = now()->toDateString();
        return view('cocina.Ordenes.historial', [
            'hoy' => $hoy,
            'puntos' => PuntoVenta::all()
        ]);
    }

    /**
     * Genera el archivo XLS de las comandas preparadas
     */
    public function obtenerReporteOrdenes(Request $request)
    {
        //Definir reglas de validacion
        $rules = [
            'f_inicio' => ['required'],
            'f_fin' => ['required'],
            'selected_pv' => ['required']
        ];

        //Validamos la peticion
        $validated = $request->validate($rules);
        //Agregamos la propiedad de eliminados
        $validated['eliminados'] = $request->input('eliminados');

        //Buscamos TODAS las cajas que coincidan entre las fechas deseadas.
        $cajas = Caja::whereDate('fecha_apertura', '>=', $validated['f_inicio'])
            ->whereDate('fecha_apertura', '<=', $validated['f_fin'])
            ->whereIn('clave_punto_venta', $validated['selected_pv'])
            ->get()
            ->toArray();


        //Hacemos la consulta (productos)
        $result = Venta::with([
            'detallesProductos' => function ($query) {
                $query->whereNotNull('id_estado');
            },
            'puntoVenta',
        ])
            ->whereIn('corte_caja', array_column($cajas, 'corte'))
            ->get();
        $result_eliminados = null;

        /*
        $result = DetallesVentaProducto::with('venta.puntoVenta', 'EstadoProductoVenta')
            ->whereHas('venta', function ($query) use ($validated) {
                $query->whereIn('clave_punto_venta', $validated['selected_pv']);
            })
            ->whereNotNull('id_estado')
            ->whereDate('inicio', '>=', $validated['f_inicio'])
            ->whereDate('inicio', '<=', $validated['f_fin'])
            ->get();
        */

        //Hacemos la consulta (productos eliminados)
        if ($validated['eliminados']) {
            $result_eliminados = Venta::with([
                'detallesProductos' => function ($query) {
                    $query->whereNotNull('id_estado')
                        ->onlyTrashed();
                },
                'puntoVenta',
            ])
                ->whereIn('corte_caja', array_column($cajas, 'corte'))
                ->get();
        }

        //dd($cajas, $result);

        //Devolvemos el excel
        return Excel::download(
            new ComandasExport($result->toArray(), $result_eliminados ?: null),
            'Reporte comandas ' .  $validated['f_inicio'] . ' - ' . $validated['f_fin'] . '.xlsx'
        );
    }
}

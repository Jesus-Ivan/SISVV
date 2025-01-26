<?php

namespace App\Http\Controllers;

use App\Imports\PeriodoImport;
use App\Models\DetallesPeriodoNomina;
use App\Models\PeriodoNomina;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdministracionController extends Controller
{
    /**
     * Dirige al usuario a la vista para cargar el archivo de excel
     */
    public function cargarPeriodo()
    {
        //Obtenemos la fecha actual
        $fecha_actual = now();
        //Si la fecha actual es menor al dia 15 del mes
        if ($fecha_actual->day <= 15) {
            //Inicio del periodo
            $inicio_periodo = $fecha_actual->now()->day(1);
            //Fin del periodo
            $fin_periodo = $fecha_actual->day(15);
        } else {
            //Inicio del periodo
            $inicio_periodo = $fecha_actual->now()->day(16);
            //Fin del periodo
            $fin_periodo = $fecha_actual->day($fecha_actual->daysInMonth);
        }

        return view(
            'administracion.Nomina.cargar-periodo',
            [
                'inicio_periodo' => $inicio_periodo->toDateString(),
                'fin_periodo' => $fin_periodo->toDateString(),
            ]
        );
    }

    /**
     * Realiza el registro de la informacion en la BD mediante una peticion http POST
     */
    public function subirPeriodo(Request $request)
    {
        $fecha_inicio = $request->input('fInicio');
        $fecha_fin = $request->input('fFin');
        $archivo = $request->file('nomina');

        try {
            //Validamos que haya un archivo
            $request->validate([
                'nomina' => [
                    'required',
                    'file',
                ]
            ]);
            //Importar la informacion
            Excel::import(new PeriodoImport($fecha_inicio, $fecha_fin), $archivo);
            //redirigimos al usuario
            return redirect()->route('administracion.cargar-p')->with('success', 'Periodo cargado correctamente');
        } catch (\Throwable $th) {
            //redirigimos al usuario
            return redirect()->route('administracion.cargar-p')->with('error', $th->getMessage());
        }
    }

    /**
     * Elimina el registro de nomina correspondiente
     */
    public function eliminarNomina($ref)
    {
        try { 
            DB::transaction(function () use ($ref) {
                //Eliminamos la informacion de la BD
                PeriodoNomina::where('referencia', $ref)->delete();
                //Eliminamos los detalles
                DetallesPeriodoNomina::where('referencia_periodo', $ref)->delete();
            });
            //redirigimos al usuario
            return redirect()
                ->route('administracion.buscar-p', ['year' => now()->year])
                ->with('success', 'Nomina eliminada correctamente');
        } catch (\Throwable $th) {
            //redirigimos al usuario
            return redirect()
                ->route('administracion.buscar-p', ['year' => now()->year])
                ->with('fail', $th->getMessage());
        }
    }

    /**
     * Realiza la carga de la vista para la busqueda e impresion del periodo de nomina
     */
    public function buscarPeriodo(Request $request)
    {
        //Verificar si se paso el QueryParam de 'year'
        if ($request->input('year')) {
            //Buscar los registros en la tabla 'periodos nomina'
            $periodos = PeriodoNomina::whereYear('created_at', $request->input('year'))
                ->orderBy('referencia', 'DESC')
                ->paginate(10);

            $periodos->appends(['year' => $request->input('year')]);
        } else {
            //Crear array vacio
            $periodos = [];
        }
        return view('administracion.Nomina.imprimir-periodo', ['periodos' => $periodos]);
    }
}

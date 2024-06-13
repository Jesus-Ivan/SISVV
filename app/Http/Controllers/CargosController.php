<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargosController extends Controller
{
    public function cargarMensualidades(Request $request)
    {
        $estado_membresia = $request->input('estado_membresia');
        $fecha = Carbon::parse($request->input('fecha'));
        $result_cuotas = DB::table('socios_cuotas')
            ->join('socios_membresias', 'socios_cuotas.id_socio', '=', 'socios_membresias.id_socio')
            ->select('socios_cuotas.*', 'socios_membresias.clave_membresia', 'socios_membresias.estado')
            ->whereNot('estado', '=', 'CAN')
            ->get();
        $cuotas = Cuota::all();

        DB::transaction(function () use ($result_cuotas,  $cuotas, $fecha) {
            foreach ($result_cuotas as $key => $socio_cuota) {
                EstadoCuenta::create([
                    'id_cuota' => $socio_cuota->id_cuota,
                    'id_socio' => $socio_cuota->id_socio,
                    'concepto' => $cuotas->find($socio_cuota->id_cuota)->descripcion . ' ' . $this->getMes($fecha->month) . '-' . $fecha->year,
                    'fecha' => $fecha->toDateString(),
                    'cargo' => $cuotas->find($socio_cuota->id_cuota)->monto,
                    'abono' => 0,
                    'saldo' => $cuotas->find($socio_cuota->id_cuota)->monto,
                ]);
            }
        }, 2);
        return 'yeeeeeih';
    }

    //Recibe un numero de mes y devuelve el mes en español
    private function getMes($fecha)
    {
        switch ($fecha) {
            case 1:
                return 'ENERO';
            case 2:
                return 'FEBRERO';
            case 3:
                return 'MARZO';
            case 4:
                return 'ABRIL';
            case 5:
                return 'MAYO';
            case 6:
                return 'JUNIO';
            case 7:
                return 'JULIO';
            case 8:
                return 'AGO';
            case 9:
                return 'SEPTIEMBRE';
            case 10:
                return 'OCTUBRE';
            case 11:
                return 'NOVIEMBRE';
            case 12:
                return 'DICIEMBRE';
            default:
                return 'error';
        }
    }
}

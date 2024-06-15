<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CargosController extends Controller
{
    public function cargarMensualidades(Request $request)
    {
        //Parseamos la fecha a una instancia de carbon
        $fecha = Carbon::parse($request->input('fecha'));
        //Obtenemos todos los socios, cuyas membresias no esten canceladas
        $socios_membresias = SocioMembresia::whereNot('estado', '=', 'CAN')
            ->whereNot('clave_membresia', '=', 'EVE')
            ->get();
        //obtenemos todas las cutoas disponibles 
        $cuotas = Cuota::all();

        //Iniciamos transaccion
        DB::transaction(function () use ($fecha, $socios_membresias, $cuotas) {
            //Para cada registro de la tabla 'socios_membresias'
            foreach ($socios_membresias as $key => $socio) {
                //buscamos registros del estado de cuenta del socio, correspondiente al mes dado por la fecha.
                $estado_cuenta = EstadoCuenta::with('cuota')->where('id_socio', $socio->id_socio)
                    ->whereYear('fecha', $fecha->year)
                    ->whereMonth('fecha', $fecha->month)
                    ->whereNotNull('id_cuota')
                    ->get();

                //Obtenemos todas las cuotas fijas (que se cargan mensualmente) del socio.
                $socio_cuotas = SocioCuota::where('id_socio', $socio->id_socio)->get();

                $estado_cuenta_separado = [];     //se encarga de almacenar los cargos del estado de cuenta, agrupados por 'id_cuota'

                //Para cada cuota existente, ingresar su par-valor al array.
                foreach ($cuotas as $index => $cuota) {
                    $estado_cuenta_separado[$cuota->id] = $estado_cuenta->where('id_cuota', $cuota->id);
                }
                /**
                 * Se obtiene un resultado como:
                 *  [
                 *    '1':[{},{}],
                 *    '2':[],
                 *    '3':[],
                 *  ]
                 *  Donde cada 'key' del array asociativo, es el 'id' de cuota de la tabla 'cuotas' y el valor es un array,
                 *  que son los todos los registros de la tabla 'estados_cuenta', que coinciden en el campo 'id_cuota' 
                 */

                $socio_cuotas_separado = [];     //se encarga de almacenar los cargos fijos del socio, agrupados por 'id_cuota'
                foreach ($cuotas as $index => $cuota) {
                    //Para cada cuota existente, ingresar el par key-value
                    $socio_cuotas_separado[$cuota->id] = $socio_cuotas->where('id_cuota', $cuota->id);
                }
                /**
                 * Se obtiene un resultado como:
                 *  [
                 *    '1':[{},{}],
                 *    '2':[],
                 *    '3':[],
                 *  ]
                 *  Donde cada 'key' del array asociativo, es el 'id' de cuota de la tabla 'cuotas' y el valor es un array,
                 *  que son los todos los cargos fijos de la tabla 'socios_cuotas', que coinciden en el campo 'id_cuota' 
                 */

                //Para cada elemento del array 'socio_cuotas_separado'
                foreach ($socio_cuotas_separado as $key => $cuota_separada) {
                    //restar cantidad de (cuotas fijas - cuotas en el estado de cuenta), con la misma "id_cuota"
                    $resta =  count($cuota_separada) - count($estado_cuenta_separado[$key]);
                    //Si hay mas cuotas del mismo 'id_cuota', que cuotas registradas en el estado de cuenta
                    if ($resta > 0) {
                        //Verficamos si se trata de cutoa de membresia.
                        if (count($cuotas->whereNotNull('clave_membresia')->where('id', $key))) {
                            //Filtramos si existe alguna cuota de membresia resgistrada, en el estado de cuenta.
                            $mensualidades_previas = array_filter($estado_cuenta->toArray(), function ($cargo) {
                                return $cargo['cuota']['clave_membresia'];
                            });
                            //Si hay al menos una cuota registrada previamente, saltar ejecucion
                            if (count($mensualidades_previas)) {
                                continue;
                            }
                        }

                        for ($i = 0; $i < $resta; $i++) {
                            EstadoCuenta::create([
                                'id_cuota' => $key,
                                'id_socio' => $socio->id_socio,
                                'concepto' => $cuotas->find($key)->descripcion . ' ' . $this->getMes($fecha->month) . '-' . $fecha->year,
                                'fecha' => $fecha->toDateString(),
                                'cargo' => $cuotas->find($key)->monto,
                                'abono' => 0,
                                'saldo' => $cuotas->find($key)->monto,
                            ]);
                        }
                    }
                }
            }
        }, 2);


        return  'todo bien, vuelve atras ;D';
    }

    public function calcularRecargos(Request $request)
    {
        //Parseamos la fecha a una instancia de carbon
        $fecha = Carbon::parse($request->input('fecha'));

        $fecha_inicio_mes = Carbon::create($fecha->year, $fecha->month, 1)->toDateString();

        //Obtenemos todos los socios, cuyas membresias no esten canceladas
        $socios_membresias = SocioMembresia::whereNot('estado', '=', 'CAN')
            ->get();
        //Obtenemos el registro correspondiente al recargo de la tabla 'cuotas'
        $recargo_cuota = Cuota::where('descripcion', 'like', '%RECARGO%')->first();

        DB::transaction(function () use ($socios_membresias, $fecha_inicio_mes, $fecha, $recargo_cuota) {
            foreach ($socios_membresias as $membresia) {
                $estado_cuenta = EstadoCuenta::where('id_socio', $membresia->id_socio)
                    ->where('vista', 'ORD')
                    ->where('saldo', '>', '0')
                    ->get();

                $estado_deuda_anterior = $estado_cuenta->where('fecha', '<', $fecha_inicio_mes);

                $estado_mes_actual = $estado_cuenta->where('id_socio', '=', $membresia->id_socio)
                    ->where('fecha', '>=', $fecha_inicio_mes)
                    ->where('fecha', '<=', $fecha->toDateString())
                    ->where('saldo', '>', '0');

                //Filtar los registros que sean notas y solo deja las cuotas del mes actual
                $estado_sin_notas = array_filter($estado_mes_actual->toArray(), function ($concepto) {
                    return $concepto['id_cuota'] || $concepto['folio_evento'];
                });

                $suma_deuda_anterior = array_sum(array_column($estado_deuda_anterior->toArray(), 'saldo'));
                $suma_deuda_actual = array_sum(array_column($estado_sin_notas, 'saldo'));

                $recargo_calculado = ($suma_deuda_anterior + $suma_deuda_actual) * 0.03;

                if ($recargo_calculado > 0) {
                    EstadoCuenta::create([
                        'id_cuota' => $recargo_cuota->id,
                        'id_socio' => $membresia->id_socio,
                        'concepto' => $recargo_cuota->descripcion . ' ' . $this->getMes($fecha->month) . '-' . $fecha->year,
                        'fecha' => $fecha->toDateString(),
                        'cargo' => $recargo_calculado,
                        'abono' => 0,
                        'saldo' => $recargo_calculado,
                    ]);
                }
            }
        }, 2);

        return 'se cargaron los recargos del 3%';
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

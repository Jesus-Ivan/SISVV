<?php

namespace App\Http\Controllers;

use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargosController extends Controller
{
    public function cargarMensualidades(Request $request)
    {
        //Parseamos la fecha a una instancia de carbon
        $fecha = Carbon::parse($request->input('fecha'));
        //Creamos fecha del mes inmediato anterior (usado para descativar anualidades)
        $fecha_previa = Carbon::parse($request->input('fecha'))->subMonth();
        //Obtenemos todos los socios, cuyas membresias no esten canceladas
        $socios_membresias = SocioMembresia::whereNot('estado', '=', 'CAN')
            ->whereNot([
                ['clave_membresia', '=', 'EVE'],
                ['clave_membresia', '=', 'INT']
            ])
            ->get();

        //Iniciamos transaccion
        DB::transaction(function () use ($fecha, $fecha_previa, $socios_membresias) {
            $expresiones = [
                'membresias' => "(ANUALIDAD (CC-I|CC-F|CG-I|CG-F)|INACTIVA|MENSUALIDAD)",
                'lockers' => "(LOKER|LOCKER|CASILLERO)",
                'resguardo' => "(RESGUARDO CARRITO)",
            ];
            //Para cada registro de la tabla 'socios_membresias'
            foreach ($socios_membresias as $key => $socio) {
                //Activamos anualidad si esta disponible
                $this->activarAnualidad($fecha, $socio->id_socio);
                //Desactivamos anualidad si tenia una activa previamente
                $this->desactivarAnualidad($fecha_previa, $socio->id_socio);

                //buscamos registros del estado de cuenta del socio, correspondiente al mes dado por la fecha.
                $estado_cuenta = EstadoCuenta::where('id_socio', $socio->id_socio)
                    ->whereYear('fecha', $fecha->year)
                    ->whereMonth('fecha', $fecha->month)
                    ->whereNotNull('id_cuota')
                    ->get()
                    ->toArray();

                //Obtenemos todas las cuotas fijas (que se cargan mensualmente) del socio.
                $socio_cuotas = SocioCuota::with('cuota')
                    ->where('id_socio', $socio->id_socio)
                    ->get()
                    ->toArray();
                //Para cada expresion regular
                foreach ($expresiones as $name => $exp) {
                    //Contamos los cargos fijos que tiene el socio que coincidan con la espresion
                    $cuotas_fijas = $this->contarCargosFijos($exp, $socio_cuotas);
                    //contamos los cargos que estan en el estado de cuenta, que coinciden con la expresion
                    $cuotas_estado = $this->contarEstadoCuenta($exp, $estado_cuenta);

                    for ($i = 0; $i < (count($cuotas_fijas) - count($cuotas_estado)); $i++) {
                        //Obtenemos el primer elemento del array asociativo (y reincia el punterto interno)
                        $cuota = reset($cuotas_fijas);
                        EstadoCuenta::create([
                            'id_cuota' => $cuota['id_cuota'],
                            'id_socio' => $socio->id_socio,
                            'concepto' => $cuota['cuota']['descripcion'] . ' ' . $this->getMes($fecha->month) . '-' . $fecha->year,
                            'fecha' => $fecha->toDateString(),
                            'cargo' => $cuota['cuota']['monto'],
                            'abono' => 0,
                            'saldo' => $cuota['cuota']['monto'],
                        ]);
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

        //Obtenemos todos los socios (excluyendo los internos y los cancelados)
        $socios_membresias = SocioMembresia::orWhere(function (Builder $query) {
            $query->whereNot('estado', '=', 'CAN')
                ->whereNot('clave_membresia', '=', 'INT');
        })->get();

        //Obtenemos el registro correspondiente al recargo de la tabla 'cuotas'
        $recargo_cuota = Cuota::where('descripcion', 'like', '%RECARGO%')->first();

        DB::transaction(function () use ($socios_membresias, $fecha_inicio_mes, $fecha, $recargo_cuota) {
            //Para cada registro de membresia
            foreach ($socios_membresias as $membresia) {
                //Obtenemos el estado de cuenta del socio, con los conceptos a deber.
                $estado_cuenta = EstadoCuenta::where('id_socio', $membresia->id_socio)
                    ->where('vista', 'ORD')
                    ->where('saldo', '>', '0')
                    ->get();
                //Separamos los conceptos que sean previos al mes al que se aplican los recargos
                $estado_deuda_anterior = $estado_cuenta->where('fecha', '<', $fecha_inicio_mes);
                //Obtenemos los conceptos del estado de cuenta, en el mes que aplican los recargos
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

    public function cargarDiferencias(Request $request)
    {
        //Recuperamos la fecha de la peticion post
        $fecha = $request->input('fecha');
        //Creamos una fecha de carbon con el dia 1 del mes anterior.
        $fechaAnterior = Carbon::parse($fecha)->day(1)->subMonth();
        //Obtenemos todas las membresias 'MEN', junto a su consumo minimo (por socio)
        $membresias = DB::table('socios_membresias')
            ->join('membresias', 'socios_membresias.clave_membresia', '=', 'membresias.clave')
            ->where('socios_membresias.estado', 'MEN')
            ->select('socios_membresias.*', 'membresias.consumo_minimo')
            ->get();
        //Buscamos la cuota correspondiente a la diferencia de consumo (multa)
        $cuota = Cuota::where('tipo', 'MUL')
            ->where('descripcion', 'like', '%CONSUMO%')
            ->first();
        //Si no hay cuota en la BD, error
        if (!$cuota) {
            throw new Exception("No se encontro la cuota de diferencia de consumo", 1);
        }

        //Creamos la transaccion
        DB::transaction(function () use ($membresias, $fechaAnterior, $fecha, $cuota) {
            //Para cada registro de membresias
            foreach ($membresias as $key => $membresia) {
                //Si la membresia no tiene consumo minimo, omitir iteracion
                if (!$membresia->consumo_minimo)
                    continue;
                //Obtenemos el consumo del mes previo del socio
                $consumo_anterior = EstadoCuenta::where('id_socio', $membresia->id_socio)
                    ->where('consumo', 1)
                    ->whereYear('fecha',  $fechaAnterior->year)
                    ->whereMonth('fecha', $fechaAnterior->month)
                    ->get();
                //Calculamos el consumo total del mes anterior
                $consumo = array_sum(array_column($consumo_anterior->toArray(), 'cargo'));
                //Realizamos la diferencia
                $diferencia = $membresia->consumo_minimo - $consumo;
                //Si la diferencia es mayor a 0, creamos un nuevo estado de cuenta
                if ($diferencia > 0) {
                    EstadoCuenta::create([
                        'id_cuota' => $cuota->id,
                        'id_socio' => $membresia->id_socio,
                        'concepto' => $cuota->descripcion . ' DE: ' . $this->getMes($fechaAnterior->month) . ' ' . $fechaAnterior->year,
                        'fecha' => $fecha,
                        'cargo' => $diferencia,
                        'abono' => 0,
                        'saldo' => $diferencia,
                        'consumo' => 0,
                    ]);
                }
            }
        }, 2);
        return 'Todo ha ido bien con las diferencias de consumo';
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

    //Esta funcion se encarga de revisar si la fecha dada como parametro, entra en la anualidad y activarla
    private function activarAnualidad(Carbon $fecha_mensualidad, $idSocio)
    {
        //Ajustamos el dia en 1
        $fecha_mensualidad->day(1);
        //Buscamos si existe anualidad para el socio, dada la fecha de la mensualidad
        $anualidad = Anualidad::where('id_socio', $idSocio)
            ->whereDate('fecha_inicio', '<=', $fecha_mensualidad->toDateString())
            ->whereDate('fecha_fin', '>=', $fecha_mensualidad->toDateString())
            ->first();
        //Si existe la anualidad
        if ($anualidad) {
            //Buscamos el estado de la membresia del socio
            $socio_membresia = SocioMembresia::where('id_socio', $idSocio)
                ->first();
            //Si no hay registro
            if (!$socio_membresia)
                throw new Exception("No se encontro registro en la tabla socios_membresias para el socio: " . $idSocio);
            //Actualizamos el estado de la membresia a anual
            $socio_membresia->estado = 'ANU';
            $socio_membresia->save();
        }
    }

    private function desactivarAnualidad(Carbon $fecha_previa, $idSocio)
    {
        //Creamos una fecha de carbon, correspondiente al mes inmediato anterior, dado el parametro fecha
        $fecha_previa->day(1);
        //Buscamos una anualidad cuya fecha_fin corresponda al mes anterior.
        $anualidad = Anualidad::where('id_socio', $idSocio)
            ->whereYear('fecha_fin', $fecha_previa->year)
            ->whereMonth('fecha_fin', $fecha_previa->month)
            ->first();
        //Si existe algun registro, cambiar estado de la membresia
        if ($anualidad) {
            //Buscamos el estado de la membresia del socio
            $socio_membresia = SocioMembresia::where('id_socio', $idSocio)
                ->first();
            //Si no hay registro
            if (!$socio_membresia)
                throw new Exception("No hay registro en la tabla socios_membresias para el socio: " . $idSocio);
            //Actualizamos el estado de la membresia a mensual
            $socio_membresia->estado = $anualidad->estado_mem_f;
            $socio_membresia->clave_membresia = $anualidad->clave_mem_f;
            $socio_membresia->save();
            //Actualizamos los cargos fijos
            if ($anualidad->estado_mem_f != 'ANU' && $anualidad->estado_mem_f != 'CAN') {
                //Buscamos los cargos fijos
                $cargos_fijos = SocioCuota::with('cuota')
                    ->where('id_socio', $idSocio)
                    ->get();
                //Si no hay cargos fijos para el socio
                if (!count($cargos_fijos))
                    throw new Exception("No hay cargos fijos para el socio: " . $idSocio);

                //Filtramos de los cargos fijos, el que corresponde a la membresia
                $cuota_mensual = array_filter($cargos_fijos->toArray(), function ($cargo) {
                    return $cargo['cuota']['clave_membresia'];
                });
                //Si no hay cuota mensual fija previamente agregrada como cargo fijo
                if (!count($cuota_mensual))
                    throw new Exception("No hay cuota de membresia fija para el socio: " . $idSocio);

                //Buscar la nueva cuota
                $cuota_mensual_nuevo = Cuota::where([
                    ['tipo', '=', $anualidad->estado_mem_f],
                    ['clave_membresia', '=', $anualidad->clave_mem_f],
                ])->first();
                //Si no hay cuota nueva para el socio
                if (!$cuota_mensual_nuevo)
                    throw new Exception("No se encontro cuota; estado de membresia " . $anualidad->estado_mem_f . ', clave de membresia ' . $anualidad->clave_mem_f);

                //Actualizamos el cargo fijo de la cuota
                SocioCuota::where('id', $cuota_mensual[0]['id'])
                    ->update(['id_cuota' => $cuota_mensual_nuevo->id]);
            }
        }
    }

    private function contarEstadoCuenta($exp_reg, $estado_cuenta)
    {
        $patron = "/$exp_reg/i";
        $estado_filtrado = array_filter($estado_cuenta, function ($cargo) use ($patron) {
            return preg_match($patron, $cargo['concepto']);
        });
        return $estado_filtrado;
    }
    private function contarCargosFijos($exp_reg, $socio_cuotas)
    {
        $patron = "/$exp_reg/i";
        $estado_filtrado = array_filter($socio_cuotas, function ($cargo) use ($patron) {
            return preg_match($patron, $cargo['cuota']['descripcion']);
        });
        return $estado_filtrado;
    }
}

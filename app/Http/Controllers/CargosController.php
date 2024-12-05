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
            //Agregamos los id's de las cuotas que corresponden
            $ids_cuotas = [
                'membresias' => [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 18, 19, 21, 22, 23, 24, 25, 26, 27, 28, 29, 43, 44, 45, 46, 51],
                'lockers' => [1],
                'resguardo' => [15],
            ];
            //Para cada registro de la tabla 'socios_membresias'
            foreach ($socios_membresias as $key => $socio) {
                //Desactivamos anualidad si tenia una activa previamente
                $anualidad_fin = $this->desactivarAnualidad($fecha_previa, $socio->id_socio);
                //Activamos anualidad si esta disponible
                $anualidad_inicio = $this->activarAnualidad($fecha, $socio->id_socio);
                //Verificar cargos fijos
                $this->verificarCargosFijos($socio->id_socio, $anualidad_inicio, $anualidad_fin);

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
                //Para cada tipo de cuota
                foreach ($ids_cuotas as $name => $ids) {
                    //Contamos los cargos fijos que tiene el socio que coincidan con la espresion
                    $cuotas_fijas = $this->contarCargos($ids, $socio_cuotas);
                    //contamos los cargos que estan en el estado de cuenta, que coinciden con la expresion
                    $cuotas_estado = $this->contarCargos($ids, $estado_cuenta);

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
            ->whereYear('fecha_inicio', $fecha_mensualidad->year)
            ->whereMonth('fecha_inicio', $fecha_mensualidad->month)
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
        return $anualidad;
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
        }
        return $anualidad;
    }

    private function verificarCargosFijos($id_socio, $anualidad_inicio, $anualidad_fin)
    {
        if ($anualidad_fin && is_null($anualidad_inicio)) {
            $this->crearCargosNuevos($anualidad_fin, $id_socio);
        } elseif (is_null($anualidad_fin) && $anualidad_inicio) {
            $this->eliminarCargosAnteriores($id_socio);
        } elseif ($anualidad_fin && $anualidad_inicio) {
            $this->eliminarCargosAnteriores($id_socio);
            $this->crearCargosNuevos($anualidad_fin, $id_socio, false);
        }
    }

    /**
     * Elimina de la tabla 'socios_cuotas', las cuotas previas de una anualidad
     */
    private function eliminarCargosAnteriores($id_socio)
    {
        //Eliminar los cargos indicados como 'auto_delete'
        SocioCuota::where([
            ['id_socio', '=', $id_socio],
            ['auto_delete', '=', true]
        ])->delete();
    }

    /**
     * Crea los nuevos cargos fijos en la tabla 'socios_cuotas'
     */
    private function crearCargosNuevos($anualidad_fin, $id_socio, $enable_mensualidad = true)
    {
        //Buscamos los detalles de la anualidad
        $detalles_anualidad = DB::table('detalles_anualidades')
            ->where('id_anualidad', $anualidad_fin->id)
            ->get();
        //Para cada detalle, cargar la cuota en la tabla 'socios_cuotas'
        foreach ($detalles_anualidad as $key => $detalle) {
            //Buscamos la cuota original que se itera
            $cuota_org = Cuota::find($detalle->id_cuota);
            //Si la cuota tiene clave de membresia
            if ($cuota_org->clave_membresia) {
                //Si no se habilito la carga de las cuotas mensuales (correspondientes a la membresia)
                if (!$enable_mensualidad)
                    continue;
                $cuota_nueva = Cuota::where([
                    ['clave_membresia', '=', $cuota_org->clave_membresia],
                    ['tipo', '=', 'MEN']
                ])->first();
            } elseif (preg_match("/LOC/i", $cuota_org->tipo)) {
                $cuota_nueva = Cuota::where([
                    ['tipo', 'like', '%LOC%'],
                    ['tipo', 'like', '%MEN%']
                ])->first();
            } elseif (preg_match("/RES/i", $cuota_org->tipo)) {
                $cuota_nueva = Cuota::where([
                    ['tipo', 'like', '%RES%'],
                    ['tipo', 'like', '%MEN%']
                ])->first();
            }
            //Agregamos cuota mensual en la tabla 'socios_cuotas'
            SocioCuota::create([
                'id_socio' => $id_socio,
                'id_cuota' => $cuota_nueva->id,
                'auto_delete' => true            //Indicador de eliminacion, para la activacion de la anualidad
            ]);
        }
    }

    private function contarCargos($ids, $socio_cuotas)
    {
        $estado_filtrado = array_filter($socio_cuotas, function ($cuota) use ($ids) {
            return in_array($cuota['id_cuota'], $ids);
        });
        return $estado_filtrado;
    }
}

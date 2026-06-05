<?php

namespace App\Http\Controllers;

use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Exception;
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
        //IDs únicos de socios activos (DISTINCT evita procesar el mismo socio N veces con múltiples membresías)
        $socios_ids = SocioMembresia::whereNot('estado', 'CAN')
            ->whereNotIn('clave_membresia', ['EVE', 'INT'])
            ->whereHas('socio')
            ->distinct()
            ->pluck('id_socio');

        //Iniciamos transaccion
        DB::transaction(function () use ($fecha, $fecha_previa, $socios_ids) {
            //Para cada socio único
            foreach ($socios_ids as $id_socio) {
                //Desactivamos anualidad si tenia una activa previamente
                $anualidad_fin = $this->desactivarAnualidad($fecha_previa, $id_socio);
                //Activamos anualidad si esta disponible
                $anualidad_inicio = $this->activarAnualidad($fecha, $id_socio);
                //Verificar cargos fijos
                $this->verificarCargosFijos($id_socio, $anualidad_inicio, $anualidad_fin);

                //Registros ya cobrados este mes para este socio
                $estado_cuenta = EstadoCuenta::where('id_socio', $id_socio)
                    ->whereYear('fecha', $fecha->year)
                    ->whereMonth('fecha', $fecha->month)
                    ->whereNotNull('id_cuota')
                    ->get()
                    ->toArray();

                //Todas las cuotas fijas del socio, agrupadas por id_cuota para detectar multiples
                $socio_cuotas = SocioCuota::with('cuota')
                    ->where('id_socio', $id_socio)
                    ->get()
                    ->groupBy('id_cuota');

                $estadoGrouped = collect($estado_cuenta)->groupBy('id_cuota');

                foreach ($socio_cuotas as $idCuota => $rows) {
                    $enEstado = $estadoGrouped->get($idCuota, collect())->count();
                    $enCuotas = $rows->count();

                    for ($i = 0; $i < ($enCuotas - $enEstado); $i++) {
                        $sc = $rows->first();
                        EstadoCuenta::create([
                            'id_cuota' => $sc->id_cuota,
                            'id_socio'  => $id_socio,
                            'concepto'  => $sc->cuota->descripcion . ' ' . $this->getMes($fecha->month) . '-' . $fecha->year,
                            'fecha'     => $fecha->toDateString(),
                            'cargo'     => $sc->monto_a_cobrar,
                            'abono'     => 0,
                            'saldo'     => $sc->monto_a_cobrar,
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

        //IDs únicos de socios activos (DISTINCT evita procesar el mismo socio N veces con múltiples membresías)
        $socios_ids = SocioMembresia::whereNot('estado', 'CAN')
            ->whereNot('clave_membresia', 'INT')
            ->whereHas('socio', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->distinct()
            ->pluck('id_socio');

        //Obtenemos el registro correspondiente al recargo de la tabla 'cuotas'
        $recargo_cuota = Cuota::where('descripcion', 'like', '%RECARGO%')->first();

        DB::transaction(function () use ($socios_ids, $fecha_inicio_mes, $fecha, $recargo_cuota) {
            //Para cada socio único
            foreach ($socios_ids as $id_socio) {
                //Obtenemos el estado de cuenta del socio, con los conceptos a deber.
                $estado_cuenta = EstadoCuenta::where('id_socio', $id_socio)
                    ->where('vista', 'ORD')
                    ->where('saldo', '>', '0')
                    ->get();
                //Separamos los conceptos que sean previos al mes al que se aplican los recargos
                $estado_deuda_anterior = $estado_cuenta->where('fecha', '<', $fecha_inicio_mes);
                //Obtenemos los conceptos del estado de cuenta, en el mes que aplican los recargos
                $estado_mes_actual = $estado_cuenta->where('id_socio', '=', $id_socio)
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
                        'id_socio' => $id_socio,
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
        //MAX(consumo_minimo) por socio entre sus membresías MEN/ANU — evita duplicados con múltiples membresías
        $socios = SocioMembresia::whereIn('estado', ['MEN', 'ANU'])
            ->whereHas('socio')
            ->join('membresias', 'socios_membresias.clave_membresia', '=', 'membresias.clave')
            ->select('socios_membresias.id_socio', DB::raw('MAX(membresias.consumo_minimo) as consumo_minimo'))
            ->groupBy('socios_membresias.id_socio')
            ->having('consumo_minimo', '>', 0)
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
        DB::transaction(function () use ($socios, $fechaAnterior, $fecha, $cuota) {
            foreach ($socios as $socio) {
                //Idempotencia: si ya existe el cargo de diferencia para este mes, omitir
                $yaExiste = EstadoCuenta::where('id_socio', $socio->id_socio)
                    ->where('id_cuota', $cuota->id)
                    ->whereYear('fecha', Carbon::parse($fecha)->year)
                    ->whereMonth('fecha', Carbon::parse($fecha)->month)
                    ->exists();
                if ($yaExiste) continue;

                //Obtenemos el consumo del mes previo del socio
                $consumo_anterior = EstadoCuenta::where('id_socio', $socio->id_socio)
                    ->where('consumo', 1)
                    ->whereYear('fecha',  $fechaAnterior->year)
                    ->whereMonth('fecha', $fechaAnterior->month)
                    ->get();
                //Calculamos el consumo total del mes anterior
                $consumo = array_sum(array_column($consumo_anterior->toArray(), 'cargo'));
                //Realizamos la diferencia
                $diferencia = $socio->consumo_minimo - $consumo;
                //Si la diferencia es mayor a 0, creamos un nuevo estado de cuenta
                if ($diferencia > 0) {
                    EstadoCuenta::create([
                        'id_cuota' => $cuota->id,
                        'id_socio' => $socio->id_socio,
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
            //Buscamos la fila exacta por clave_membresia (evita tocar fila arbitraria con múltiples membresías)
            $socio_membresia = SocioMembresia::where('id_socio', $idSocio)
                ->where('clave_membresia', $anualidad->clave_mem_f)
                ->first();
            if (!$socio_membresia)
                throw new Exception("No se encontro registro en la tabla socios_membresias para el socio: " . $idSocio);
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
            //Buscamos la fila exacta por clave_membresia (evita tocar fila arbitraria con múltiples membresías)
            $socio_membresia = SocioMembresia::where('id_socio', $idSocio)
                ->where('clave_membresia', $anualidad->clave_mem_f)
                ->first();
            if (!$socio_membresia)
                throw new Exception("No hay registro en la tabla socios_membresias para el socio: " . $idSocio);
            //Actualizamos el estado de la membresia al estado previo registrado en la anualidad
            $socio_membresia->estado = $anualidad->estado_mem_f;
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

}

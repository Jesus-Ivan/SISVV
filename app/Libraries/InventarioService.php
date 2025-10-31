<?php

namespace App\Libraries;

use App\Constants\AlmacenConstants;
use App\Models\DetallesRequisicion;
use App\Models\Grupos;
use App\Models\Insumo;
use App\Models\MovimientosAlmacen;
use App\Models\Presentacion;
use Carbon\Carbon;
use Exception;

class InventarioService
{

    /**
     * Esta funcion consulta las existencias de las presentaciones. 
     */
    public function consultarPresentaciones(Grupos $grupoInsumo, $fecha_inv, $hora_inv, $clave_bodega)
    {
        //Preparamos la fecha limite
        $fechaLimite = Carbon::parse($fecha_inv . $hora_inv);
        //Array auxiliar
        $result = [];
        //Array de condiciones 
        $condiciones = [
            ['fecha_existencias', '<=', $fechaLimite->toDateTimeString()],
            ['clave_bodega', '=', $clave_bodega]
        ];
        //Obtenemos las presentaciones activas con las existencias
        $existencias = Presentacion::query()
            ->where([
                ['estado', '=', 1],
                ['id_grupo', '=', $grupoInsumo->id]
            ])
            ->withSum([
                'movimientosAlmacen' => fn($query) => $query->where($condiciones)
            ], 'cantidad_presentacion')
            ->withSum([
                'movimientosAlmacen' => fn($query) => $query->where($condiciones)
            ], 'cantidad_insumo')
            ->get();

        //Simplificar el array y agregar atributo extra.
        foreach ($existencias as $row) {
            $result[] = [
                'clave' => $row->clave,
                'descripcion' => $row->descripcion,
                'id_grupo' => $row->id_grupo,
                'costo' => $row->costo,
                'iva' => $row->iva,
                'costo_con_impuesto' => $row->costo_con_impuesto,
                'clave_insumo_base' => $row->clave_insumo_base,
                'rendimiento' => $row->rendimiento,
                'existencias_presentacion' => $row->movimientos_almacen_sum_cantidad_presentacion ?: 0,
                'existencias_insumo' => $row->movimientos_almacen_sum_cantidad_insumo ?: 0,
                'existencias_real' => $row->movimientos_almacen_sum_cantidad_presentacion ?: 0,
                'diferencia' => 0,
                'diferencia_importe' => 0,
                'clave_concepto' => '',
                'ultima_compra' => $row->ultima_compra
            ];
        }

        return $result;
    }

    /**
     * Esta funcion consulta las existencias de los insumos. Segun el grupo al que pertenece
     */
    public function consultarInsumos(Grupos $grupoInsumo, $fecha_inv, $hora_inv, $clave_bodega)
    {
        //Preparamos la fecha limite
        $fechaLimite = Carbon::parse($fecha_inv . $hora_inv);
        //Array auxiliar
        $result = [];
        //Array de condiciones 
        $condiciones = [
            ['fecha_existencias', '<=', $fechaLimite->toDateTimeString()],
            ['clave_bodega', '=', $clave_bodega]
        ];
        //Obtenemos las presentaciones activas con las existencias
        $existencias = Insumo::query()->with('unidad')
            ->where([
                ['inventariable', '=', 1],
                ['id_grupo', '=', $grupoInsumo->id]
            ])
            ->withSum([
                'movimientosAlmacen' => fn($query) => $query->where($condiciones)
            ], 'cantidad_insumo')
            ->get();

        //Simplificar el array y agregar atributo extra.
        foreach ($existencias as $row) {
            $result[] = [
                'clave' => $row->clave,
                'descripcion' => $row->descripcion,
                'id_grupo' => $row->id_grupo,
                'unidad_descripcion' => $row->unidad->descripcion,
                'costo' => $row->costo,
                'iva' => $row->iva,
                'costo_con_impuesto' => $row->costo_con_impuesto,
                'clave_insumo_base' => $row->clave_insumo_base,
                'rendimiento' => $row->rendimiento,
                'existencias_insumo' => $row->movimientos_almacen_sum_cantidad_insumo ?: 0,
                'existencias_real' => $row->movimientos_almacen_sum_cantidad_insumo ?: 0,
                'diferencia' => 0,
                'diferencia_importe' => 0,
                'clave_concepto' => '',
                'ultima_compra' => $row->ultima_compra
            ];
        }

        return $result;
    }

    /**
     * Genera el array inicial, con los nombres de TODOS los insumos a consultar (en los N grupos).\
     * Ademas, agrega las claves de las bodegas, como columnas al array
     */
    public function obtenerTodosInsumos(array|null $claves_grupos, $bodegas, $folio = null)
    {
        $result = [];
        //si hay un folio
        if ($folio) {
            //Obtener las presentaciones de la requisicion
            $detalles = DetallesRequisicion::with('presentacion')
                ->where('folio_requisicion', $folio)->get();
            //Buscar cada insumo base
            foreach ($detalles as $key => $detalle) {
                $result[] = Insumo::with('unidad')
                    ->select('clave', 'descripcion', 'id_grupo', 'id_unidad', 'ultima_compra')
                    ->where('clave', $detalle->presentacion->clave_insumo_base)
                    ->first();
            }
        } else {
            //Obtener todos los insumos activos, incluidos en algun grupo.
            $result = Insumo::with('unidad')
                ->select('clave', 'descripcion', 'id_grupo', 'id_unidad', 'ultima_compra')
                ->where('inventariable', 1)
                ->whereIn('id_grupo', $claves_grupos)
                ->get();
        }
        $aux = [];
        //Para cada insumo obtenido previamente
        foreach ($result as  $insumo) {
            //Indexarlo en el array auxiliar, con su clave de la BD.
            $aux[$insumo->clave] = $insumo->toArray();
            //Agregar las claves de las bodegas.
            foreach ($bodegas as $bodega) {
                $aux[$insumo->clave][$bodega->clave] = null;
            }
        }
        return $aux;
    }


    /**
     * Esta funcionn consulta las existencias de un insumo. con su clave de insumo y su clave de bodega
     */
    public function existenciasInsumo($clave_insumo, $fecha_inv, $hora_inv, $clave_bodega)
    {
        //Preparamos la fecha limite
        $fechaLimite = Carbon::parse($fecha_inv . $hora_inv);
        //Array auxiliar para almacenar los insumos.
        $result = [];
        //Array de condiciones 
        $condiciones = [
            ['fecha_existencias', '<=', $fechaLimite->toDateTimeString()],
            ['clave_bodega', '=', $clave_bodega]
        ];
        //Obtenemos los insumos que coincidan con la clave y sus existencias
        $existencias = Insumo::query()
            ->where('clave', $clave_insumo)
            ->withSum([
                'movimientosAlmacen' => fn($query) => $query->where($condiciones)
            ], 'cantidad_insumo')
            ->get();

        //Simplificar el array y agregar atributo extra.
        foreach ($existencias as $row) {
            $result[] = [
                'clave' => $row->clave,
                'descripcion' => $row->descripcion,
                'costo' => $row->costo,
                'iva' => $row->iva,
                'costo_con_impuesto' => $row->costo_con_impuesto,
                'clave_insumo_base' => $row->clave_insumo_base,
                'existencias_insumo' => $row->movimientos_almacen_sum_cantidad_insumo ?: 0,
                'existencias_real' => $row->movimientos_almacen_sum_cantidad_presentacion ?: 0,
                'ultima_compra' => $row->ultima_compra
            ];
        }

        return $result;
    }

    /**
     * Consulta las existencias de los insumos en base a una requisicon, 
     */
    public function obtenerExistenciasRequi($folio, $fecha_inv, $hora_inv, $clave_bodega, $tipo_bodega)
    {
        //Preparamos la fecha limite
        $fechaLimite = Carbon::parse($fecha_inv . $hora_inv);

        //Obtener las presentaciones de la requisicion
        $detalles = DetallesRequisicion::with('presentacion')
            ->where('folio_requisicion', $folio)
            ->get();

        //Buscar cada insumo base de la requisicion
        foreach ($detalles as $key => $detalle) {
            $insumos[] = Insumo::select('clave', 'descripcion', 'id_grupo', 'id_unidad', 'ultima_compra')
                ->where('clave', $detalle->presentacion->clave_insumo_base)
                ->first();
        }

        //Array auxiliar
        $result = [];
        //Array de condiciones para la consulta
        $condiciones = [
            ['fecha_existencias', '<=', $fechaLimite->toDateTimeString()],
            ['clave_bodega', '=', $clave_bodega]
        ];

        if ($tipo_bodega == AlmacenConstants::INSUMOS_KEY) {
            //Consultar cada existencia insumos asociados a la requisicion 
            foreach ($insumos as $insumo) {
                //Obtener las existencias de 1 insumo
                $existencias_insumo = Insumo::query()->with('unidad')
                    ->where('clave', $insumo->clave)
                    ->withSum([
                        'movimientosAlmacen' => fn($query) => $query->where($condiciones)
                    ], 'cantidad_insumo')
                    ->first();

                $result[] = [
                    'clave' => $existencias_insumo->clave,
                    'descripcion' => $existencias_insumo->descripcion,
                    'unidad_descripcion' => $existencias_insumo->unidad->descripcion,
                    'costo' => $existencias_insumo->costo,
                    'iva' => $existencias_insumo->iva,
                    'costo_con_impuesto' => $existencias_insumo->costo_con_impuesto,
                    'existencias_insumo' => $existencias_insumo->movimientos_almacen_sum_cantidad_insumo ?: 0,
                    'existencias_real' => $existencias_insumo->movimientos_almacen_sum_cantidad_insumo ?: 0,
                    'ultima_compra' => $existencias_insumo->ultima_compra
                ];
            }
        } elseif ($tipo_bodega == AlmacenConstants::PRESENTACION_KEY) {
            //Consultar cada existencia de las presentaciones asociadas a la requisicion 
            foreach ($detalles as $detalle) {
                //Obtener las existencias de 1 presentacion
                $existencias_pre = Presentacion::query()
                    ->where('clave', $detalle->clave_presentacion)
                    ->withSum([
                        'movimientosAlmacen' => fn($query) => $query->where($condiciones)
                    ], 'cantidad_presentacion')
                    ->first();

                $result[] = [
                    'clave' => $existencias_pre->clave,
                    'descripcion' => $existencias_pre->descripcion,
                    'costo' => $existencias_pre->costo,
                    'iva' => $existencias_pre->iva,
                    'costo_con_impuesto' => $existencias_pre->costo_con_impuesto,
                    'clave_insumo_base' => $existencias_pre->clave_insumo_base,
                    'existencias_presentacion' => $existencias_pre->movimientos_almacen_sum_cantidad_presentacion ?: 0,
                    'existencias_real' => $existencias_pre->movimientos_almacen_sum_cantidad_presentacion ?: 0,
                    'ultima_compra' => $existencias_pre->ultima_compra
                ];
            }
        }
        return $result;
    }

    /**
     * Obtener la ruta de la vista, segun el tipo de bodega
     */
    public function getView($tipo_bodega)
    {
        if ($tipo_bodega == AlmacenConstants::INSUMOS_KEY) {
            $view_path = 'reportes.existencias.existencias-insumos';    //vista del reporte para los insumos
        } elseif ($tipo_bodega == AlmacenConstants::PRESENTACION_KEY) {
            $view_path = 'reportes.existencias.existencias-presentaciones';    //vista del reporte para las presentaciones
        }

        return $view_path;
    }

    /**
     * Calcula el importe segun la cantidad dada. (redondeado a dos decimales)
     */
    public function obtenerImporte(float $costo_unitario, float $cant): float
    {
        //Calcular el importe
        return round($cant * $costo_unitario, 2);
    }

    /**
     * Obtiene la sumatoria de los movimientos de almacen. segun el concepto del movimientos y la bodega registrada
     */
    public function obtenerMovimientosConceptos(array $clave_conceptos, $fecha_inicio, $fecha_fin, $clave_bodega)
    {
        //Obtener el total de los movimientos, agrupados por clave_insumo
        $movimientos = MovimientosAlmacen::select('clave_insumo')
            ->selectRaw('SUM(cantidad_insumo) as total_cantidad')
            ->whereDate('fecha_existencias', '>=', $fecha_inicio)
            ->whereDate('fecha_existencias', '<=', $fecha_fin)
            ->whereIn('clave_concepto', $clave_conceptos)
            ->where('clave_bodega', $clave_bodega)
            ->groupBy('clave_insumo')
            ->get()
            ->toArray();
        //Extraer las claves
        $keys = array_column($movimientos, 'clave_insumo');
        //Combinar las claves y los movimientos correspondientes
        $final = array_combine($keys, $movimientos);
        return $final;
    }

    /**
     * Actualiza el costo de la presentacion y del insumo base
     */
    public function actualizarCostoPresen($row, $fecha)
    {
        //Buscar la presentacion, junto al insumo base
        $presentacion = Presentacion::with('insumo')->find($row['clave']);
        $insumo = $presentacion->insumo;
        //nueva fecha de entrada
        $nuevaFecha = $fecha;
        //Fechas actual de "ultima compra"
        $fechaExistente_presen = $presentacion->ultima_compra;
        $fechaExistente_insum = $insumo->ultima_compra;

        if (is_null($fechaExistente_presen) || $nuevaFecha >= $fechaExistente_presen) {
            //Actualizar valores de la presentacion
            $presentacion->costo = $row['costo'];
            $presentacion->iva = $row['iva'];
            $presentacion->costo_con_impuesto = $row['costo_con_impuesto'];
            $presentacion->costo_rend = $row['costo'] / $presentacion->rendimiento;
            $presentacion->costo_rend_impuesto = $row['costo_con_impuesto'] / $presentacion->rendimiento;
            $presentacion->ultima_compra = $nuevaFecha;
            $presentacion->save();
        }
        if (is_null($fechaExistente_insum) || $nuevaFecha >= $fechaExistente_insum) {
            //Actualizar valores del insumo base
            $insumo->costo = $row['costo'] / $presentacion->rendimiento;
            $insumo->iva = $row['iva'];
            $insumo->costo_con_impuesto = $row['costo_con_impuesto'] / $presentacion->rendimiento;
            $insumo->ultima_compra = $nuevaFecha;
            $insumo->save();
        }
    }

    /**
     * Actualiza el costo del insumo base y de la presentacion\
     * Nota: en caso de tener mas de 1 presentacion, actualiza todas las presentaciones.
     */
    public function actualizarCostoInsum($row, $fecha)
    {
        //Buscar el insumo base y su presentacion(nes)
        $insu = Insumo::with('presentaciones')->find($row['clave']);
        $presentaciones = $insu->presentaciones;
        //nueva fecha de entrada
        $nuevaFecha = $fecha;
        //fecha actual de "ultima compra"
        $fechaExistente_insum = $insu->ultima_compra;

        if (count($presentaciones) > 0) {
            if (is_null($fechaExistente_insum) || $nuevaFecha >= $fechaExistente_insum) {
                //Actualizar valores de todas las presentaciones
                foreach ($presentaciones as $key => $p) {
                    //Actualizar valores de la presentacion
                    $p->costo = $row['costo'] * $p->rendimiento;
                    $p->iva = $row['iva'];
                    $p->costo_con_impuesto = $row['costo_con_impuesto'] * $p->rendimiento;
                    $p->costo_rend = $row['costo'];
                    $p->costo_rend_impuesto = $row['costo_con_impuesto'];
                    $p->ultima_compra = $nuevaFecha;
                    $p->save();
                }

                //Actualizar los costos del insumo
                $insu->costo = $row['costo'];
                $insu->iva = $row['iva'];
                $insu->costo_con_impuesto = $row['costo_con_impuesto'];
                $insu->ultima_compra = $nuevaFecha;
                $insu->save();
            }
        } else {
            throw new Exception("No hay presentaciones asignadas al insumo: " . $insu->descripcion);
        }
    }
}

<?php

namespace App\Libraries;

use App\Models\DetallesRequisicion;
use App\Models\Grupos;
use App\Models\Insumo;
use App\Models\Presentacion;
use Carbon\Carbon;

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
     * Esta funcion consulta las existencias de los insumos. 
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
    public function obtenerTodosInsumos(array $claves_grupos, $bodegas, $folio = null)
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
     * Consulta las esistencias de los insumos en base a una requisicon
     */
    public function existenciasInsumoRequi(){

    }
}

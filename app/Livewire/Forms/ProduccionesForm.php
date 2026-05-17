<?php

namespace App\Livewire\Forms;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\DetalleTransformacion;
use App\Models\MovimientosAlmacen;
use App\Models\Transformacion;
use Carbon\Carbon;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProduccionesForm extends Form
{
    //Atributos
    public $insumos_elaborados = [];

    /**
     * Convierte el modelo del insumo y agrega las propiedades necesarias\
     * Agrega el insumo convertido a la lista 'insumos_elaborados'
     */
    public function agregarInsumo($insumo)
    {
        //Convertir a array
        $insum_array = $insumo->toArray();
        $insum_array['cantidad'] = 1;
        $insum_array['total_elaborado'] = $insum_array['rendimiento_elaborado'];
        array_push($this->insumos_elaborados, $insum_array);
    }


    /**
     * Calcula el rendimiento elaborado del insumo\
     * segun el indice dado en la tabla principal
     */
    public function calcularTotalElaborado($key)
    {
        //Obtenemos una copia de la fila afectada
        $row = $this->insumos_elaborados[$key];
        //Si cantidad no esta definida, o es null
        if (!$row['cantidad']) {
            $row['cantidad'] = 1;
        }
        //realizar la multiplicacion
        $row['total_elaborado'] = $row['cantidad'] *  $row['rendimiento_elaborado'];
        //Reemplazar valor de la tabla
        $this->insumos_elaborados[$key] = $row;
    }

    /**
     * Reemplaza la receta, por los nuevos valores.\
     * Segun el insumo elaborado seleccionado
     */
    public function reemplazarReceta($receta, $index_insumo_elaborado)
    {
        $this->insumos_elaborados[$index_insumo_elaborado]['receta'] = $receta;
    }

    /**
     * Guarda la produccion en las tablas
     * 'transformaciones' y 'detalles_transformacion'
     */
    public function guardarProduccion(array $validated): Transformacion
    {
        //Valida si hay insumos elaborados en la propiedad del formulario
        if (!count($this->insumos_elaborados))
            throw new Exception("Ingresa al menos 1 insumo elaborado para producir", 1);
        //Crear hora auxiliar
        $fecha_hora = Carbon::parse($validated['fecha_existencias'])->setTimeFromTimeString($validated['hora_existencias'])->toDateTimeString();
        //Crea la produccion
        $trans = Transformacion::create([
            'id_user' => auth()->user()->id,
            'user_name' => auth()->user()->name,
            'clave_origen' => $validated['clave_origen'],
            'clave_destino' => $validated['clave_destino'],
            'observaciones' => $validated['observaciones'],
            'fecha_existencias' => $fecha_hora,
        ]);
        //crea los detalles de la produccion
        foreach ($this->insumos_elaborados as $key => $insumo_elaborado) {
            foreach ($insumo_elaborado['receta'] as $i => $r) {
                //Calcular total sin merma
                $t_sin_merma = $insumo_elaborado['cantidad'] * $r['cantidad'];
                $t_con_merma = $insumo_elaborado['cantidad'] * $r['cantidad_c_merma'];
                //Calcular total con merma
                DetalleTransformacion::create([
                    'folio_transformacion' => $trans->folio,
                    'clave_insumo_elaborado' => $insumo_elaborado['clave'],
                    'cantidad' => $insumo_elaborado['cantidad'],
                    'rendimiento' => $insumo_elaborado['rendimiento_elaborado'],
                    'total_elaborado' => $insumo_elaborado['total_elaborado'],
                    'clave_insumo_receta' => $r['clave_insumo'],
                    'cantidad_insumo' => $r['cantidad'],
                    'cantidad_con_merma' => $r['cantidad_c_merma'],
                    'total_sin_merma' => $t_sin_merma,
                    'merma' => $t_con_merma - $t_sin_merma,
                    'total_con_merma' => $t_con_merma,
                ]);
            }
        }
        return $trans;
    }

    /**
     * Esta funcion realiza los movimientos de salida de almacen\
     * 'Salida por produccion'
     */
    public function crearSalida(Transformacion $trans)
    {
        //Recorremos todos los insumos elaborados en la produccion
        foreach ($this->insumos_elaborados as $key => $insumo_elaborado) {
            //Para cada insumo elaborado, recorrer su receta
            foreach ($insumo_elaborado['receta'] as $i => $r) {
                //Calcular valores auxiliares
                $t_sin_merma = $insumo_elaborado['cantidad'] * $r['cantidad'];
                $t_con_merma = $insumo_elaborado['cantidad'] * $r['cantidad_c_merma'];
                $merma = $t_con_merma - $t_sin_merma;
                //Crear movimiento almacen
                MovimientosAlmacen::create([
                    'folio_transformacion' => $trans->folio,
                    'clave_concepto' => AlmacenConstants::SAL_PROD_KEY,
                    'clave_insumo' => $r['clave_insumo'],
                    'descripcion' => $r['ingrediente']['descripcion'],
                    'clave_bodega' => $trans->clave_origen,
                    'cantidad_insumo' => -$t_sin_merma,
                    'costo' => 0,
                    'iva' => 0,
                    'costo_con_impuesto' => 0,
                    'importe' => 0,
                    'fecha_existencias' => $trans->fecha_existencias,
                ]);
                //Si hay merma
                if ($merma > 0) {
                    //Registrar la merma
                    MovimientosAlmacen::create([
                        'folio_transformacion' => $trans->folio,
                        'clave_concepto' => AlmacenConstants::SAL_MER_KEY,
                        'clave_insumo' => $r['clave_insumo'],
                        'descripcion' => $r['ingrediente']['descripcion'],
                        'clave_bodega' => $trans->clave_origen,
                        'cantidad_insumo' => -$merma,
                        'costo' => 0,
                        'iva' => 0,
                        'costo_con_impuesto' => 0,
                        'importe' => 0,
                        'fecha_existencias' => $trans->fecha_existencias,
                    ]);
                } elseif ($merma < 0) {
                    throw new Exception("Merma no puede ser negativa, revisar receta para: " . $insumo_elaborado['descripcion']);
                }
            }
        }
    }

    /**
     * Esta funcion crea los movimientos de entrada de almacen
     * 'Entrada por produccion'
     */
    public function crearEntrada(Transformacion $trans)
    {
        //Recorremos todos los insumos elaborados en la produccion
        foreach ($this->insumos_elaborados as $key => $insumo_elaborado) {
            //Crear movimiento almacen
            MovimientosAlmacen::create([
                'folio_transformacion' => $trans->folio,
                'clave_concepto' => AlmacenConstants::ENT_PROD_KEY,
                'clave_insumo' => $insumo_elaborado['clave'],
                'descripcion' => $insumo_elaborado['descripcion'],
                'clave_bodega' => $trans->clave_destino,
                'cantidad_insumo' => $insumo_elaborado['total_elaborado'],
                'costo' => 0,
                'iva' => 0,
                'costo_con_impuesto' => 0,
                'importe' => 0,
                'fecha_existencias' => $trans->fecha_existencias,
            ]);
        }
    }

    /**
     * Calcula las existencias de la receta del elaborado seleccionado\
     * Agregra al array de la receta, el atributo de las existencias de origen
     */
    public function actualizarExistencias($index, $fecha, $hora, $clave_bodega)
    {
        //Servicio de inventario
        $service = new InventarioService();
        //Del atributo original en el formulario, crear una copia temporal de la receta
        $receta_temp = $this->insumos_elaborados[$index]['receta'];

        //Consultar las existencias de cada insumo de la receta
        $receta_temp = array_map(function ($insum) use ($service, $fecha, $hora, $clave_bodega) {
            $result = $service->existenciasInsumo($insum['clave_insumo'], $fecha, $hora, $clave_bodega);
            //Agregar el atributo al array
            $insum['existencias_origen'] = reset($result)['existencias_insumo'];
            return $insum;
        }, $receta_temp);
        //Devolver la receta actualizada
        return $receta_temp;
    }

    /**
     * Limpia todos los valores del formulario
     */
    public function limpiar()
    {
        $this->reset();
    }
}

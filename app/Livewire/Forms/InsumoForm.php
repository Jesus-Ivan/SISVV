<?php

namespace App\Livewire\Forms;

use App\Models\Insumo;
use App\Models\Receta;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InsumoForm extends Form
{
    public $descripcion, $id_grupo, $id_unidad;
    public $costo, $iva = 16, $costo_iva;
    public $ultima_compra = "", $inventariable = true, $elaborado = false;
    public $rendimiento;

    public $subtable = [];   //Almacena los insumos requeridos para la elaboracion.

    public ?Insumo $original = null;


    /**
     * Guarda los valores iniciales del insumo, para establecerlos dentro del formulario como array
     */
    public function setValues(Insumo $insumo)
    {
        //Resguardar el insumo original
        $this->original = $insumo;
        //Guardar propiedades editables
        $this->descripcion = $insumo->descripcion;
        $this->id_grupo = $insumo->id_grupo;
        $this->id_unidad = $insumo->id_unidad;
        $this->costo = $insumo->costo;
        $this->iva = $insumo->iva;
        $this->costo_iva = $insumo->costo_con_impuesto;
        $this->ultima_compra = $insumo->ultima_compra;
        $this->inventariable = boolval($insumo->inventariable);
        $this->elaborado = boolval($insumo->elaborado);
        $this->rendimiento = $insumo->rendimiento_elaborado;
        //buscar los ingredientes (insumos que necesita)
        $result = Receta::with('ingrediente')
            ->where('clave_insumo_elaborado', $insumo->clave)
            ->get();
        foreach ($result as $key => $insumo) {
            $this->subtable[] = [
                'id' => $insumo->id,
                'clave' => $insumo->clave_insumo,
                'descripcion' => $insumo->ingrediente->descripcion,
                'cantidad' => $insumo->cantidad,
                'cantidad_c_merma' => $insumo->cantidad_c_merma,
                'unidad' => ['descripcion' => $insumo->ingrediente->unidad->descripcion],
                'costo_con_impuesto' => $insumo->ingrediente->costo_con_impuesto,
                'total' => $insumo->total,
            ];
        }
    }

    /**
     * Guarda el insumo en la base de datos
     */
    public function guardarNuevoInsumo()
    {
        //Si es un insumo elaborado
        if ($this->elaborado) {
            //Validar las propiedades si se trata de un insumo elaborado
            $validated = $this->validate([
                'descripcion' => 'required',
                'id_grupo' => 'required',
                'id_unidad' => 'required',
                'costo_iva' => 'required|numeric',
                'rendimiento' => 'required|numeric',
                'subtable' => 'min:1'
            ]);
            $this->validarReceta();
        } else {
            //Validar las propiedades de un insumo normal
            $validated = $this->validate([
                'descripcion' => 'required',
                'id_grupo' => 'required',
                'id_unidad' => 'required',
                'costo_iva' => 'required|numeric',
            ]);
        }

        //agregar las propiedades faltantes
        $validated['costo'] = $this->costo;
        $validated['iva'] = $this->iva;
        $validated['inventariable'] = $this->inventariable;
        $validated['elaborado'] = $this->elaborado;
        $validated['rendimiento'] = $this->rendimiento;
        $validated['ultima_compra'] = $this->ultima_compra;

        DB::transaction(function () use ($validated) {
            //Crear el insumo
            $result = Insumo::create([
                'descripcion' => $validated['descripcion'],
                'id_grupo' => $validated['id_grupo'],
                'id_unidad' => $validated['id_unidad'],
                'costo' => $validated['costo'],
                'ultima_compra' => strlen($validated['ultima_compra']) > 0 ? $validated['ultima_compra'] : null,
                'iva' => $validated['iva'],
                'costo_con_impuesto' => $validated['costo_iva'],
                'inventariable' => $validated['inventariable'],
                'elaborado' => $validated['elaborado'],
                'rendimiento_elaborado' => $validated['rendimiento'],
            ]);
            //Crear los detalles del insumo elaborado
            if ($validated['elaborado']) {
                foreach ($validated['subtable'] as $key => $insumo) {
                    Receta::create([
                        'clave_insumo_elaborado' => $result->clave,
                        'clave_insumo' => $insumo['clave'],
                        'cantidad' => $insumo['cantidad'],
                        'cantidad_c_merma' => $insumo['cantidad_c_merma'],
                        'total' => $insumo['total'],
                    ]);
                };
            }
        }, 2);
        //reset las propiedades
        $this->reset();
    }

    /**
     * Actualiza las modificaciones del insumo en la BD.
     */
    public function actualizarInsumo()
    {
        //Validar los campos si es un insumo elaborado
        if ($this->elaborado) {
            //Validar las propiedades si se trata de un insumo elaborado
            $validated = $this->validate([
                'descripcion' => 'required',
                'id_grupo' => 'required',
                'id_unidad' => 'required',
                'costo_iva' => 'required|numeric',
                'rendimiento' => 'required|numeric',
                'subtable' => 'min:1'
            ]);
            $this->validarReceta();
        } else {
            //Validar las propiedades de un insumo normal
            $validated = $this->validate([
                'descripcion' => 'required',
                'id_grupo' => 'required',
                'id_unidad' => 'required',
                'costo_iva' => 'required|numeric',
            ]);
        }

        //Realizar transaccion
        DB::transaction(function () use ($validated) {
            //Guardar los cambios en el insumo
            $this->original->descripcion = $validated['descripcion'];
            $this->original->id_grupo = $validated['id_grupo'];
            $this->original->id_unidad = $validated['id_unidad'];
            $this->original->costo = $this->costo;
            $this->original->iva = $this->iva;
            $this->original->costo_con_impuesto = $validated['costo_iva'];
            $this->original->inventariable = $this->inventariable;
            $this->original->elaborado = $this->elaborado;
            //Si es un insumo elaborado, verificar sus propiedades extra
            if ($this->elaborado) {
                $this->original->rendimiento_elaborado = $validated['rendimiento'];
                foreach ($validated['subtable'] as $key => $insumo) {
                    //Si contiene el atributo 'deleted'
                    if (array_key_exists('deleted', $insumo)) {
                        //Si hay atributo 'id'
                        if (array_key_exists('id', $insumo))
                            //Eliminacion suave de la BD
                            Receta::destroy($insumo['id']);
                    } elseif (array_key_exists('id', $insumo)) {
                        //Actualizar el registro
                        Receta::where('id', $insumo['id'])
                            ->update([
                                'cantidad' => $insumo['cantidad'],
                                'cantidad_c_merma' => $insumo['cantidad_c_merma'],
                                'total' => $insumo['total'],
                            ]);
                    } else {
                        //crear el nuevo registro del insumo requerido para la receta
                        Receta::create([
                            'clave_insumo_elaborado' => $this->original->clave,
                            'clave_insumo' => $insumo['clave'],
                            'cantidad' => $insumo['cantidad'],
                            'cantidad_c_merma' => $insumo['cantidad_c_merma'],
                            'total' => $insumo['total'],
                        ]);
                    }
                }
            }
            $this->original->save(); //Persistir informacion
        }, 2);
    }

    /**
     * Agregar un insumo a la tabla de insumo elaborado
     */
    public function agregarInsumo($clave)
    {
        //Buscar el insumo seleccionado
        $result = Insumo::with('unidad')->find($clave);
        //Convertir en array el insumo
        $result = $result->toArray();
        //Agregar propiedades necesarias para la tabla (cantidad y total) de insumo elaborado
        $result['cantidad'] = null;
        $result['cantidad_c_merma'] = null;
        $result['total'] = 0;
        //Añadir al array
        $this->subtable[] = $result;
    }

    public function calcularPrecioIva()
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->iva) == 0)
            $this->iva = '0';
        //Verificar el atributo $costo es un string vacio 
        if (strlen($this->costo) == 0)
            $this->costo = '0';
        //Calcular costo con iva
        $costo_iva = $this->costo + ($this->costo * ($this->iva / 100));
        $this->costo_iva = round($costo_iva, 2);
    }
    public function calcularPrecioSinIva()
    {
        //Verificar el atributo $costo_iva es un string vacio 
        if (strlen($this->costo_iva) == 0)
            $this->costo_iva = '0';
        //Calcular Costo sin iva
        $costo_sin_iva = ($this->costo_iva * 100) / (100 + $this->iva);
        $this->costo = round($costo_sin_iva, 2);
    }

    /**
     * Elimina el insumo de la tabla de insumo-elaborado
     */
    public function eliminarInsumoSeleccionado($index)
    {
        //Eliminar el item del array de insumo elaborado
        unset($this->subtable[$index]);
        //Multiplicar cada elemento de la tabla (costo_con_impuesto * cantidad)
        foreach ($this->subtable as $key => $value) {
            $this->recalcularSubtotales($key);
        }
    }

    /**
     * Agrega el atributo 'deleted' de forma temporal. En el array de insumo elaborado
     */
    public function marcarInsumo($index)
    {
        $this->subtable[$index]['deleted'] = true;
    }

    /**
     * Multiplica la fila de la tabla de insumos (elaborado).\
     * total = cantidad_c_merma * costo_con_impuesto.\
     * Actualiza el costo_con_impuesto del insumo general
     */
    public function recalcularSubtotales($index)
    {
        //Obtenemos la fila original
        $row =  $this->subtable[$index];
        //Si 'cantidad' es un string vacio 
        if (strlen($row['cantidad']) == 0)
            $row['cantidad'] = '1';       //asignar un nuevo valor
        //Si 'cantidad_c_merma' es un string vacio 
        if (strlen($row['cantidad_c_merma']) == 0)
            $row['cantidad_c_merma'] = '1';       //asignar un nuevo valor
        $row['total'] = round($row['cantidad_c_merma'] * $row['costo_con_impuesto'], 2);

        //Actualizar la tabla
        $this->subtable[$index] = $row;

        //Calcular el costo con impuesto del insumo elaborado
        $this->costo_iva = round(array_sum(array_column($this->subtable, 'total')), 2);
        //calcular el costo sin iva
        $this->calcularPrecioSinIva();
    }

    /**
     * Contiene las reglas para validar los insumos de la receta.\
     */
    public function validarReceta()
    {
        foreach ($this->subtable as $index => $insumo) {
            $this->validate([
                'subtable.' . $index . '.cantidad' => 'required',
                'subtable.' . $index . '.cantidad_c_merma'  => 'required'
            ], [
                'subtable.*.cantidad.required' => 'Obligatorio',
                'subtable.*.cantidad_c_merma.required' => 'Obligatorio',
            ]);
        }
    }
}

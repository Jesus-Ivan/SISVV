<?php

namespace App\Livewire\Forms;

use App\Libraries\InventarioService;
use App\Models\DetallesCompra;
use App\Models\DetallesRequisicion;
use App\Models\OrdenCompra;
use App\Models\Requisicion;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Ramsey\Uuid\Type\Integer;

class RequisicionesForm extends Form
{
    public $tipo_orden, $observaciones;         //Tipo de orden a registrar y observaciones
    //Tabla de presentaciones para la requisicion
    public $presentaciones = [];

    public ?Requisicion $requi_original = null;

    /**
     * Establece los valores iniciales para editar la orden de compra
     */
    public function setValues(Requisicion $requisicion)
    {
        //Guardar el modelo original
        $this->requi_original = $requisicion;
        //Guardar el tipo de orden
        $this->tipo_orden = $requisicion->tipo_orden;
        //Guardar las observaciones
        $this->observaciones = $requisicion->observaciones;
        //Buscar los detalles de la requisicion
        $result = DetallesRequisicion::where('folio_requisicion', $requisicion->folio)->get();
        //Limpiar la tabla
        $this->presentaciones = [];
        //Agregarlos al array de presentaciones, (utilizado para la vista)
        foreach ($result as $detalle) {
            $this->presentaciones[] = [
                'id' => $detalle->id,
                'clave' => $detalle->clave_presentacion,
                'descripcion' => $detalle->descripcion,
                'id_proveedor' => $detalle->id_proveedor,
                'cantidad' => $detalle->cantidad,
                'costo_unitario' => $detalle->costo_unitario,
                'iva' => $detalle->iva,
                'costo_con_impuesto' => $detalle->costo_con_impuesto,
                'impuesto' => $detalle->impuesto,
                'importe_sin_impuesto' => $detalle->importe_sin_impuesto,
                'importe' =>  $detalle->importe,
            ];
        }
    }

    /**
     * Agrega al array 'presentaciones' la presentacion validada
     */
    public function agregarPresentacion($presentacion)
    {
        //Instacia de clase del inventario
        $invService = new InventarioService();
        //Obtener el importe sin impuesto
        $importe_sin_impuesto = $invService->obtenerImporte($presentacion['costo_unitario'], 1);
        //Obtener el importe con impuesto
        $importe = $invService->obtenerImporte($presentacion['costo_con_impuesto'], 1);

        //Array de datos
        $this->presentaciones[] = [
            'clave' => $presentacion['articulo_seleccionado']['clave'],
            'descripcion' => $presentacion['articulo_seleccionado']['descripcion'],
            'id_proveedor' => $presentacion['id_proveedor'],
            'cantidad' => 1,
            'costo_unitario' => $presentacion['costo_unitario'],
            'iva' => $presentacion['iva'],
            'costo_con_impuesto' => $presentacion['costo_con_impuesto'],
            'impuesto' => $importe - $importe_sin_impuesto,
            'importe_sin_impuesto' => $importe_sin_impuesto,
            'importe' => $importe,
        ];
    }

    /**
     * Elimina del array 'presentaciones' segun el indice
     */
    public function eliminarPresentacion($index)
    {
        unset($this->presentaciones[$index]);
    }

    /**
     * Agregar el atributo 'deleted' al item
     */
    public function marcarPresentacion($index)
    {
        $this->presentaciones[$index]['deleted'] = true;
    }

    /**
     * Multiplica la cantidad de la presentacion * el costo con impuesto, segun el indice de la tabla 'presentaciones'
     */
    public function actualizarImporte($index)
    {
        //Verificar que cantidad no sea vacio
        if (strlen($this->presentaciones[$index]['cantidad']) == 0)
            $this->presentaciones[$index]['cantidad'] = 1;
        //Variable auxiliar
        $row = $this->presentaciones[$index];
        //Instacia de clase del inventario
        $inventarioService = new InventarioService();

        //Calcular calcular el importe_sin_impuesto
        $importe_sin_impuesto = $inventarioService->obtenerImporte($row['costo_unitario'], $row['cantidad']);
        //Calcular importe con impuesto
        $importe = $inventarioService->obtenerImporte($row['costo_con_impuesto'], $row['cantidad']);
        //Actualizar valores
        $this->presentaciones[$index]['impuesto'] = $importe - $importe_sin_impuesto;
        $this->presentaciones[$index]['importe_sin_impuesto'] = $importe_sin_impuesto;
        $this->presentaciones[$index]['importe'] = $importe;
    }

    /**
     * Calcula el costo con iva de la tabla 'presentaciones'
     */
    public function actualizarCostoIva($index)
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->presentaciones[$index]['iva']) == 0)
            $this->presentaciones[$index]['iva'] = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->presentaciones[$index]['costo_unitario']) == 0)
            $this->presentaciones[$index]['costo_unitario'] = '0';

        $costo_unitario = $this->presentaciones[$index]['costo_unitario']; //variables axuliares
        $iva = $this->presentaciones[$index]['iva']; //variables axuliares

        //Calcular costo con iva
        $costo_iva = $costo_unitario + ($costo_unitario * ($iva / 100));
        $this->presentaciones[$index]['costo_con_impuesto'] = round($costo_iva, 2);
    }

    /**
     * Calcula el costo unitario sin iva de la tabla 'presentaciones', segun el indice dado
     */
    public function actualizarCostoSinIva($index)
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->presentaciones[$index]['costo_con_impuesto']) == 0)
            $this->presentaciones[$index]['costo_con_impuesto'] = '0';

        //Variables auximilares
        $costo_con_impuesto = $this->presentaciones[$index]['costo_con_impuesto'];
        $iva = $this->presentaciones[$index]['iva'];

        //Calcular Costo sin iva
        $costo_sin_iva = ($costo_con_impuesto * 100) / (100 + $iva);
        $this->presentaciones[$index]['costo_unitario'] = round($costo_sin_iva, 2);
    }

    /**
     * Crea en la BD, los registros correspondientes a la requisicion
     */
    public function crearRequisicion()
    {
        //Multiplicar los valores de la tabla, por si hubo error en una peticion de livewire
        $this->multiplicarTabla();

        $validated = $this->validate([
            'tipo_orden' => 'required',
            'presentaciones' => 'required|array|min:1',
        ]);
        $subtotal = $this->calcularSubtotal($validated['presentaciones']);
        $iva = $this->calcularIva($validated['presentaciones']);
        //creamos la orden
        $result_orden = Requisicion::create([
            'id_user' => auth()->user()->id,
            'tipo_orden' => $validated['tipo_orden'],
            'observaciones' => $this->observaciones,
            'movimientos' => count($validated['presentaciones']),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $subtotal + $iva,
        ]);

        foreach ($validated['presentaciones'] as $key => $articulo) {
            //creamos los detalles de la orden de compra
            DetallesRequisicion::create([
                'folio_requisicion' => $result_orden->folio,
                'clave_presentacion' => $articulo['clave'],
                'descripcion' => $articulo['descripcion'],
                'id_proveedor' => $articulo['id_proveedor'],
                'cantidad' => $articulo['cantidad'],
                'costo_unitario' => $articulo['costo_unitario'],
                'iva' => $articulo['iva'],
                'costo_con_impuesto' => $articulo['costo_con_impuesto'],
                'importe_sin_impuesto' => $articulo['importe_sin_impuesto'],
                'impuesto' => $articulo['impuesto'],
                'importe' => $articulo['importe'],
            ]);
        }
        $this->reset();
    }

    /**
     * Obtiene la sumatoria de ('importe_sin_impuesto')
     */
    public function calcularSubtotal($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Si la presentacion contiene el atributo eliminado, omitir
            if (array_key_exists('deleted', $presentacion)) continue;
            //Mutiplicar y acumular el valor
            $acu += $presentacion['importe_sin_impuesto'];
        }
        return $acu;
    }

    /**
     * Obtiene la sumatoria de ('impuesto')
     */
    private function calcularIva($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Si la presentacion contiene el atributo eliminado, omitir
            if (array_key_exists('deleted', $presentacion)) continue;
            //Mutiplicar y acumular el valor
            $acu += $presentacion['impuesto'];
        }
        return $acu;
    }

    /**
     * Crea, elimina y actualiza los cambios en la requisicion
     */
    public function guardarRequisicion()
    {
        //Multiplicar los valores de la tabla, por si hubo error en una peticion de livewire
        $this->multiplicarTabla();

        $validated = $this->validate([
            'tipo_orden' => 'required',
        ]);
        $validated['observaciones'] = $this->observaciones; //Agregar la propiedad al array validado

        //Calcular el subtotal y el nuevo iva
        $subtotal = $this->calcularSubtotal($this->presentaciones);
        $iva = $this->calcularIva($this->presentaciones);

        //Actualizar los detalles de la orden
        foreach ($this->presentaciones as $key => $item) {
            //Si contiene el atributo 'deleted'
            if (array_key_exists('deleted', $item)) {
                //Si hay atributo 'id'
                if (array_key_exists('id', $item))
                    //Eliminacion suave de la BD
                    DetallesRequisicion::destroy($item['id']);
            } elseif (array_key_exists('id', $item)) {
                //Actualizar el registro
                DetallesRequisicion::where('id', $item['id'])
                    ->update([
                        'cantidad' => $item['cantidad'],
                        'costo_unitario' => $item['costo_unitario'],
                        'iva' => $item['iva'],
                        'costo_con_impuesto' => $item['costo_con_impuesto'],
                        'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                        'impuesto' => $item['impuesto'],
                        'importe' => $item['importe']
                    ]);
            } else {
                //crear el nuevo registro de la presentacion, en la requisicion
                DetallesRequisicion::create([
                    'folio_requisicion' => $this->requi_original->folio,
                    'clave_presentacion' => $item['clave'],
                    'descripcion' => $item['descripcion'],
                    'id_proveedor' => $item['id_proveedor'],
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'iva' => $item['iva'],
                    'costo_con_impuesto' => $item['costo_con_impuesto'],
                    'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                    'impuesto' => $item['impuesto'],
                    'importe' => $item['importe'],
                ]);
            }
        }

        //Actualizar el total de la orden
        Requisicion::where('folio', $this->requi_original->folio)
            ->first()
            ->update([
                'tipo_orden' => $validated['tipo_orden'],
                'observaciones' => $validated['observaciones'],
                'cantidad' => $this->movimientos($this->presentaciones),
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $subtotal + $iva
            ]);
        $requi = Requisicion::find($this->requi_original->folio);
        //Setear los nuevos valores editables en el form
        $this->setValues($requi);
    }

    /**
     * Obtiene el numero de items no eliminados del array
     */
    public function movimientos(array $array): int
    {
        $result = array_filter($array, function ($item) {
            return ! array_key_exists('deleted', $item);
        });
        return count($result);
    }

    /**
     * Mutiplica la tabla de presentaciones, para verificar el costo sin iva y el importe
     */
    public function multiplicarTabla()
    {
        foreach ($this->presentaciones as $key => $value) {
            $this->actualizarCostoSinIva($key);
            $this->actualizarImporte($key);
        }
    }
}

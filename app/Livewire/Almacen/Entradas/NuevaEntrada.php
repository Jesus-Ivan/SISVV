<?php

namespace App\Livewire\Almacen\Entradas;

use App\Constants\AlmacenConstants;
use App\Models\CatalogoVistaVerde;
use App\Models\DetallesCompra;
use App\Models\DetallesEntrada;
use App\Models\Entrada as EntradaModel;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use App\Models\UnidadCatalogo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NuevaEntrada extends Component
{

    public $folio_search,  $orden_result = [];
    public $tipo_compra_general = null;
    public $fecha_compra_general = null;

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function unidades()
    {
        return Unidad::all();
    }

    public function buscarOrden()
    {
        //Si existe una orden de compra
        if (OrdenCompra::find($this->folio_search)) {
            //Buscar los detalles de la orden de compra
            $result = DetallesCompra::with('unidadCatalogo')
                ->where('folio_orden', $this->folio_search)
                ->where('aplicado', false)
                ->get()
                ->toArray();

            //Agregar el campos a la consulta
            $this->orden_result = array_map(function ($row) {
                $row['fecha_compra'] = null;                //agregamos la fecha al array
                $row['peso'] = $this->revisarUnidad($this->unidades, $row, "KG", 'only');      //agregamos el peso al array
                $row['cantidad'] = $this->revisarUnidad($this->unidades, $row, "KG", 'exclusive');  //modificamos la cantidad array
                $row['importe'] = 0;                        //modificamos el importe array
                $row['tipo_compra'] = $this->tipo_compra_general;                //modificamos el importe array
                return $row;
            }, $result);
            $this->calculateTable();
        } else {
            //limpiar el arreglo
            $this->orden_result = [];
        }
    }

    //Limpia todos los campos de entrada de una fila
    public function limpiarCampos($index)
    {
        $this->orden_result[$index]['id_unidad'] = null;
        $this->orden_result[$index]['fecha_compra'] = null;
        $this->orden_result[$index]['peso'] = null;
        $this->orden_result[$index]['cantidad'] = null;
        $this->orden_result[$index]['importe'] = 0;
        $this->orden_result[$index]['tipo_compra'] = null;
    }

    public function changeTipoCompra($eValue)
    {
        //Cambiamos el tipo de compra de cada articulo
        array_walk($this->orden_result, function (&$value, $key) use ($eValue) {
            $value['tipo_compra'] = $eValue;
        });
    }

    public function changeFecha($eValue)
    {
        //Cambiamos la fecha de compra de cada articulo
        array_walk($this->orden_result, function (&$value, $key) use ($eValue) {
            $value['fecha_compra'] = $eValue;
        });
    }

    //Esta funcion multiplica la cantidad y el costo unitario de cada fila de la tabla y actualiza el importe
    public function calculateTable()
    {
        $new_result = array_map(function ($row) {
            if ($row['peso']) {
                $row['importe'] = $row['peso'] * $row['costo_unitario'];
            } elseif ($row['cantidad']) {
                $row['importe'] = $row['cantidad'] * $row['costo_unitario'];
            } else {
                $row['importe'] = 0;
            }

            return $row;
        }, $this->orden_result);
        $this->orden_result = $new_result;
    }

    public function aplicarEntrada()
    {
        //Validar que el array no este vacio
        $validated = $this->validate([
            'orden_result' => 'required|min:1'
        ], [
            'orden_result.required' => 'La orden de compra es requerida',
            'orden_result.min' => 'Orden de compra invalida',
        ]);

        //Volvemos a multiplicar toda la tabla, si hubo algun error de red
        $this->calculateTable();

        try {
            //Filtramos los elementos de la orden de compra, que tienen algun dato en la tabla
            $detalles_orden = array_filter($validated['orden_result'], function ($producto) {
                return $producto['tipo_compra']
                    || $producto['cantidad']
                    || $producto['peso']
                    || $producto['fecha_compra']
                    || $producto['id_unidad'];
            });
            //Si no hay detalles de orden con datos en la tabla
            if (! count($detalles_orden)) {
                //Error al aplicar entrada sin informacion
                throw new Exception('Debes completar por lo menos un articulo');
            }

            //Verificamos cada stock de cada articulo que se desea dar entrada
            foreach ($detalles_orden as $row) {
                //Comprobar si tiene el tipo de compra
                if (!$row['tipo_compra'])
                    throw new Exception("Revisa: " . $row['nombre'] . ", falta tipo de compra");
                //Comprobar si tiene unidad
                if (!$row['id_unidad'])
                    throw new Exception("Revisa: " . $row['nombre'] . ", falta unidad");
                //Comprobar si el stock ingresado es valido (no null)
                if (!($row['cantidad'] || $row['peso']))
                    throw new Exception("Revisa: " . $row['nombre'] . ", falta cantidad o peso", 1);
                //Comprobar si tiene fecha de compra
                if (!($row['fecha_compra']))
                    throw new Exception("Revisa: " . $row['nombre'] . ", falta fecha de compra", 1);
            }
            //Empezamos la transaccion
            DB::transaction(function () use ($validated, $detalles_orden) {
                /**
                 * Calculamos los valores necesarios
                 * */
                $subtotal = array_sum(array_column($detalles_orden, 'importe'));
                $iva = array_sum(array_column($detalles_orden, 'iva'));
                //Creamos el registro de la entrada en la BD
                $entrada = EntradaModel::create([
                    'folio_orden_compra' => reset($validated['orden_result'])['folio_orden'],
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $subtotal + $iva
                ]);

                //Creamos los detalles_entrada, y sus acciones secundarias
                foreach ($detalles_orden as $key => $row) {
                    //Cambiamos el estado a 'aplicado' en el "detalles_compra"
                    DetallesCompra::where('id', $row['id'])
                        ->update(['aplicado' => true]);
                    //Actualizamos el precio por unidad del producto
                    $this->actualizarPrecioUnidad($row);
                    //Actualizamos la fecha de ultima compra en el inventario ("catalogo_vista_verde")
                    $this->actualizarUltimaCompra($row);

                    //Creamos el registro del detalle de entrada
                    DetallesEntrada::create([
                        'id_proveedor' => $row['id_proveedor'],
                        'codigo_producto' => $row['codigo_producto'],
                        'folio_entrada' => $entrada['folio'],
                        'nombre' => $row['nombre'],
                        'cantidad' => $row['cantidad'] ?: null,
                        'peso' => $row['peso'] ?: null,
                        'costo_unitario' => $row['costo_unitario'],
                        'importe' => $row['importe'],
                        'tipo_compra' => $row['tipo_compra'],
                        'iva' =>  $row['iva'],
                        'fecha_compra' =>  $row['fecha_compra'],
                    ]);

                    //Aumentamos los stock, del producto
                    $this->actualizarStocks($row);
                }
            }, 2);
            session()->flash('success-entrada', 'Entrada aplicada con exito!'); //Informacion de action message
            $this->reset();                 //limpiamos el componente
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage()); //Informacion de action message
        }
        $this->dispatch('entrada');     //Emitimos evento para abrir acction message 
    }

    /**
     * Actualiza la fecha de la columna 'ultima_compra' de la tabla 'catalogo_vista_verde'
     * Siempre y cuando la fecha ingresada en la entrada sea mayor (o null)
     */
    private function actualizarUltimaCompra($producto)
    {
        //Buscamos el producto por su codigo
        $result = CatalogoVistaVerde::find($producto['codigo_producto']);
        if (! $result) {
            throw new Exception("No se encontro el producto " . $producto['codigo_producto'], 1);
        }
        //Si el registro no tiene fecha de ultima compra
        if (! $result->ultima_compra) {
            $result->ultima_compra = $producto['fecha_compra']; //Actualizar la ultima compra, con la fecha dada
            $result->save();                              //Guardamos el cambio en la BD
        } else {
            //Creamos un objetos Carbon para comprar fechas
            $fecha_compra = Carbon::parse($producto['fecha_compra']);
            //Si la fecha de compra es mayor que la ultima compra, actualizamos la ultima compra
            if ($fecha_compra->greaterThan($result->ultima_compra)) {
                $result->ultima_compra = $producto['fecha_compra']; //Actualizar la fecha
                $result->save();                              //Guardamos el cambio en la BD
            }
        }
    }

    private function actualizarStocks($producto)
    {
        //Buscamos los stocks en la DB, del producto
        $result = Stock::where('codigo_catalogo', $producto['codigo_producto'])
            ->get();

        //Filtrar del resultado de la query previa, las filas cuya columna 'tipo' sea unitario
        $stock_unitario = $result->where('tipo', AlmacenConstants::CANTIDAD_KEY)->first();
        //Filtrar del resultado de la query previa, las filas cuyo campo 'tipo' sea peso
        $stock_peso = $result->where('tipo', AlmacenConstants::PESO_KEY)->first();

        //si el usuario ingreso un valor en el input de cantidad.
        if ($producto['cantidad']) {
            //Si hay stock unitario en la BD, actualizamos el stock unitario de almacen
            if ($stock_unitario) {
                $stock_unitario->stock_alm += $producto['cantidad'];
                $stock_unitario->save();    //Guardamos el stock
            } else {
                //Si no hay stock unitario, creamos uno nuevo
                $result_stock = Stock::create([
                    'codigo_catalogo' => $producto['codigo_producto'],
                    'tipo' => AlmacenConstants::CANTIDAD_KEY
                ]);
                $result_stock->stock_alm += $producto['cantidad'];
                $result_stock->save();    //Guardamos el stock
            }
        }

        if ($producto['peso']) {
            if ($stock_peso) {
                $stock_peso->stock_alm += $producto['peso'];
                $stock_peso->save();        //Guardamos el stock
            } else {
                //Si no hay stock de peso en la BD, creamos uno nuevo
                $result_stock = Stock::create([
                    'codigo_catalogo' => $producto['codigo_producto'],
                    'tipo' => AlmacenConstants::PESO_KEY
                ]);
                $result_stock->stock_alm += $producto['peso'];
                $result_stock->save();    //Guardamos el stock
            }
        }
    }

    /**
     * Actualiza la columna 'costo_unidad' de la tabla 'unidad_catalogo', segun los valores ingresados en la entrada
     */
    private function actualizarPrecioUnidad($producto)
    {
        UnidadCatalogo::where([
            ['codigo_catalogo', '=', $producto['codigo_producto']],
            ['id_unidad', '=', $producto['id_unidad']]
        ])
            ->update(['costo_unidad' => $producto['costo_unitario']]);
    }

    /**
     * Revisa si la unidad de la fila dada, coincide con la descripcion, tomando como referencia la BD.
     * Devuelve la cantidad solicitada del articulo, segun la unidad
     */
    private function revisarUnidad($unidades, $row, $descripcion_unidad = "KG", $mode)
    {
        $unidad_buscada = $unidades
            ->where('descripcion', '=', $descripcion_unidad)
            ->first();

        if ($mode  == 'only') {
            if ($unidad_buscada->id == $row['id_unidad'])
                return  $row['cantidad'];
        }
        if ($mode  == 'exclusive') {
            if ($unidad_buscada->id != $row['id_unidad'])
                return  $row['cantidad'];
        }
    }

    public function render()
    {
        return view(
            'livewire.almacen.entradas.nueva-entrada',
            ['metodo_pago' => AlmacenConstants::METODOS_PAGO]
        );
    }
}

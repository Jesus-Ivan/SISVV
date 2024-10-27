<?php

namespace App\Livewire\Almacen\Entradas;

use App\Models\CatalogoVistaVerde;
use App\Models\DetallesCompra;
use App\Models\DetallesEntrada;
use App\Models\Entrada as EntradaModel;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NuevaEntrada extends Component
{

    public $folio_search,  $orden_result = [];

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    public function buscarOrden()
    {
        //Si existe una orden de compra
        if (OrdenCompra::find($this->folio_search)) {
            //Buscar los detalles de la orden de compra
            $result = DetallesCompra::where('folio_orden', $this->folio_search)
                ->where('aplicado', false)
                ->get()
                ->toArray();

            //Agregar el campos a la consulta
            $this->orden_result = array_map(function ($row) {
                $row['fecha_compra'] = null;        //agregamos la fecha al array
                $row['peso'] = null;                   //agregamos el peso al array
                $row['cantidad'] = null;               //modificamos la cantidad array
                $row['importe'] = 0;                //modificamos el importe array
                return $row;
            }, $result);
        } else {
            //limpiar el arreglo
            $this->orden_result = [];
        }
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
            //Empezamos la transaccion
            DB::transaction(function () use ($validated) {
                //Filtramos los elementos de la orden de entrada, que tienen fecha de compra
                $detalles_orden = array_filter($validated['orden_result'], function ($producto) {
                    return $producto['fecha_compra'];
                });

                //Verificamos cada stock de cada articulo que se desea dar entrada
                foreach ($detalles_orden as $row) {
                    //Comprobar si el stock ingresado es valido (no null)
                    if (!($row['cantidad'] || $row['peso']))
                        throw new Exception("Revisa: " . $row['nombre'] . ", falta cantidad o peso", 1);
                }

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
                    //Actualizamos la fecha de ultima compra en el inventario
                    $this->actualizarUltimaCompra($row);
                    
                    //Creamos el registro del detalle de entrada
                    DetallesEntrada::create([
                        'id_proveedor' => $row['id_proveedor'],
                        'codigo_producto' => $row['codigo_producto'],
                        'folio_entrada' => $entrada['folio'],
                        'nombre' => $row['nombre'],
                        'cantidad' => $row['cantidad'],
                        'peso' => $row['peso'],
                        'costo_unitario' => $row['costo_unitario'],
                        'importe' => $row['importe'],
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
        $stock_unitario = $result->where('tipo', 'unitario')->first();
        //Filtrar del resultado de la query previa, las filas cuyo campo 'tipo' sea peso
        $stock_peso = $result->where('tipo', 'peso')->first();

        //si el usuario ingreso un valor en el input de cantidad.
        if ($producto['cantidad']) {
            //Si hay stock unitario en la BD, actualizamos el stock unitario de almacen
            if ($stock_unitario) {
                $stock_unitario->stock_alm += $producto['cantidad'];
                $stock_unitario->save();    //Guardamos el stock
            }
        }

        if ($producto['peso']) {
            if ($stock_peso) {
                $stock_peso->stock_alm += $producto['peso'];
                $stock_peso->save();        //Guardamos el stock
            }
        }
    }

    public function render()
    {
        return view('livewire.almacen.entradas.nueva-entrada');
    }
}

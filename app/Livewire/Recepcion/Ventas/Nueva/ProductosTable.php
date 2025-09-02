<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoVistaVerde;
use App\Models\Producto;
use Dotenv\Exception\ValidationException;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductosTable extends Component
{
    #[Modelable]
    public $productos = [];  //Almacenamos los productos, de forma temporal

    #[Computed]
    public function total()
    {
        return array_sum(array_column($this->productos, 'subtotal'));
    }

    //Escuchamos el evento del modal
    #[On('producto-seleccionado')]
    public function onSelectProducto(array $producto, $cantidad)
    {
        //Buscar el producto original
        $prod = Producto::find($producto['clave']);
        try {
            //Agregarlo a la tabla
            $this->agregarProducto($prod, $cantidad, time(), true);
        } catch (ValidationException $th) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $th;
        } catch (Exception $e) {
            //Mensaje de sesion para el error
            session()->flash('fail', $e->getMessage());
        }
    }



    public function removeProduct($chunk)
    {
        //Filtar aquellos items con diferente chunk
        $filtrados = array_filter($this->productos, function ($row) use ($chunk) {
            return $row['chunk'] != $chunk;
        });
        $this->productos = $filtrados;
    }

    //Funcion para modificar la cantidad y el subtotal del array de productos.
    public function updateQuantity($productoIndex, $newCantidad)
    {
        // Update the quantity in the productos array
        $this->productos[$productoIndex]['cantidad'] = $newCantidad;
        // Recalculate the subtotal based on the new quantity
        $this->productos[$productoIndex]['subtotal'] = $this->productos[$productoIndex]['cantidad'] * $this->productos[$productoIndex]['precio'];
    }

    /**
     * Agrega el NUEVO producto a la tabla
     */
    public function agregarProducto(Producto $producto, int $cantidad, int $chunk, $autoSum = false)
    {
        //Filtramos el producto(de la tabla) que coincida con la clave del producto a ingresar (y no sea modificador)
        $p_filtrado = array_filter($this->productos, function ($item) use ($producto) {
            return $item['clave_producto'] == $producto->clave
                && !array_key_exists('modif', $item);
        });
        //Si la opcion de autosuma esta activada, y hay un producto con la misma clave en la tabla
        if ($autoSum && count($p_filtrado)) {
            //Agregar la cantidad el producto segun el indice del producto filtrado
            foreach ($p_filtrado as $key => $value) {
                if ($cantidad <= 0 && abs($cantidad) >= $this->productos[$key]['cantidad']) {
                    //Lanzar excepcion
                    throw new Exception("Negativo superior");
                }
                $this->productos[$key]['cantidad'] += $cantidad;
                $this->productos[$key]['subtotal'] += $cantidad * $producto->precio_con_impuestos;
                break;
            }
        } else {
            //Si no hay producto con la clave (en la tabla) y la cantidad es negativa
            if (count($p_filtrado) == 0 && $cantidad <= 0) {
                //Lanzar excepcion
                throw new Exception("Cant. inicial invalida");
            }
            //Agregar directamente el producto
            $this->productos[] = [
                'chunk' => $chunk,
                'clave_producto' => $producto->clave,
                'nombre' => $producto->descripcion,
                'cantidad' => $cantidad,
                'precio' => $producto->precio_con_impuestos,
                'subtotal' => $cantidad * $producto->precio_con_impuestos,
            ];
        }
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-table');
    }
}

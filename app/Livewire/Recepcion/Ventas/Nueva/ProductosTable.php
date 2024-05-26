<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoVistaVerde;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductosTable extends Component
{
    #[Modelable]
    public $productos = [];  //Almacenamos los productos, de forma temporal

    #[Computed]
    public function total(){
        return array_sum(array_column($this->productos, 'subtotal'));
    }

    //Escuchamos el evento del modal
    #[On('productosSeleccionados')]
    public function onFinishSelect(array $total_seleccionados)
    {
        foreach ($total_seleccionados as $key => $value) {
            //Buscamos el producto en la base de datos
            $product = CatalogoVistaVerde::find($key);
            //Agregamos el producto a la lista de productos
            array_push($this->productos, [
                'temp' => time(),
                'codigo_catalogo' => $key,
                'nombre' => $product->nombre,
                'cantidad' => 1,
                'precio' => $product->costo_unitario,
                'subtotal' => $product->costo_unitario,
                'inicio' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    

    public function removeProduct($productoIndex){
        //Eliminar el producto del array de productos
        unset($this->productos[$productoIndex]);
    }

    //Funcion para modificar la cantidad y el subtotal del array de productos.
    public function updateQuantity($productoIndex, $newCantidad)
    {
        // Update the quantity in the productos array
        $this->productos[$productoIndex]['cantidad'] = $newCantidad;
        // Recalculate the subtotal based on the new quantity
        $this->productos[$productoIndex]['subtotal'] = $this->productos[$productoIndex]['cantidad'] * $this->productos[$productoIndex]['precio'];
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-table');
    }
}

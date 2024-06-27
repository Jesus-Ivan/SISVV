<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaForm extends Form
{
    public $tipoVenta = "socio";    //El tipo de venta a realizar
    public $nombre_invitado;        //El nombre del invitado
    public $socioSeleccionado;      //El socio seleccionado

    public $socioPago;              //El socio seleccionado para agregar en metodo de pago
    public $id_pago;                //id del tipo de pago seleccionado en el modal
    public $monto_pago;             //el monto a pagar
    public $proprina;               //Si dejo o no propina

    public $seachProduct = '';        //Input de busqueda de productos
    public $selected = [];            //Almacena los codigos de productos seleccionados del modal.

    public $productosTable = [];      //array de productos, que se muestran en la tabla (productos agregados)
    public $pagosTable = [];          //Array de pagos que se muestran en la tabla
    public $totalVenta = 0;           //El costo total de los articulos


    /* Agrega los articulos seleccionados a la tabla.
        La funcion recibe el array de todos los items mostrado en el modal.
    */
    public function agregarItems($productosResult)
    {
        //Filtramos los productos seleccionados, cuyo valor sea true del checkBox
        $total_seleccionados = array_filter($this->selected, function ($val) {
            return $val;
        });

        //Si no se han seleccionado articulos, impedir ejecuccion
        if (!count($total_seleccionados) > 0) {
            return false;
        }
        //Recorrer todo el array de seleccionados
        foreach ($total_seleccionados as $key => $value) {
            //Se busca el registro del producto en base a su codigo.
            $producto = $productosResult->find($key);
            //Se anexa el producto al array de la tabla
            $this->productosTable[] = [
                'codigo_catalogo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio' => $producto->costo_unitario,
                'subtotal' => $producto->costo_unitario,
                'observaciones' => '',
                'tiempo' => null
            ];
        }

        //Limpiamos las propiedades
        $this->selected = [];    //Productos seleccionados
        $this->actualizarTotal();
    }

    public function eliminarArticulo($productoIndex)
    {
        unset($this->productosTable[$productoIndex]);
        $this->actualizarTotal();
    }

    public function calcularSubtotal($productoIndex, $eValue)
    {
        //Se actualiza la cantidad del producto en la tabla
        $this->productosTable[$productoIndex]['cantidad'] = $eValue;
        //Se calcula el subtotal del producto
        $this->productosTable[$productoIndex]['subtotal'] = $this->productosTable[$productoIndex]['precio'] * $eValue;
        $this->actualizarTotal();
    }

    public function incrementarProducto($productoIndex)
    {
        //Definimos una variable que apunta a la direccion de memoria del producto
        $articulo = &$this->productosTable[$productoIndex];
        //incrementamos en 1 la cantidad
        $articulo['cantidad']++;
        //Obtenemos el nuevo subtotal
        $articulo['subtotal'] = $articulo['cantidad'] * $articulo['precio'];
    }

    public function decrementarProducto($productoIndex)
    {
        //Definimos una variable que apunta a la direccion de memoria del producto
        $articulo = &$this->productosTable[$productoIndex];
        //Comprobamos si la cantidad es positiva
        if ($articulo['cantidad'] > 1) {
            $articulo['cantidad']--;
            $articulo['subtotal'] = $articulo['cantidad'] * $articulo['precio'];
        }
    }

    public function actualizarTotal()
    {
        //Se actualiza el total de los productos
        $this->totalVenta = array_sum(array_column($this->productosTable, 'subtotal'));
    }
}

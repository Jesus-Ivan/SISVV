<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaForm extends Form
{
    public $tipoVenta = "socio";    //El tipo de venta a realizar
    public $nombre_invitado;        //El nombre del invitado
    public $socioSeleccionado;      //El socio seleccionado

    public $seachProduct = '';        //Input de busqueda de productos
    public $productosTable = [];      //array de productos, que se muestran en la tabla
    public $pagosTable = [];          //Array de pagos que se muestran en la tabla

    public $selected = [];               //Almacena los elementos seleccionados del modal de productos

    //Agrega los articulos seleccionados a la tabla
    public function agregarItems($productosResult)
    {
        //Filtramos los productos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->selected, function ($val) {
            return $val;
        });

        $productos = array_map(function ($key, $value) use ($productosResult) {
            $product = $productosResult->find($key);
            return [
                'codigo' => $product->codigo,
                'nombre' => $product->nombre,
                'precio' => $product->costo_unitario,
                'cantidad' => 2,
                'subtotal' => 2 * $product->costo_unitario,
            ];
        }, $total_seleccionados,[]);
        //Si ha seleccionado al menos 1 articulo
        if (count($total_seleccionados) > 0) {
            //Guardamos los articulos que recien selecciono
            $this->productosTable = $productos;
            //Reseteamos el componente
            $this->reset();
        }
    }
}

<?php

namespace App\Livewire\Almacen\Existencias;

use App\Models\CatalogoVistaVerde;
use App\Models\DetallesCompra;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Reporte extends Component
{
    //Los campos de entrada correspondientes al folio que se busca o el articulo especifico
    public $folio_input, $seachProduct;
    //Casilla de sumar seleccion
    public $autosuma = true;
    //Lista de articulos que se van a reportar
    public $lista_articulos = [];
    //Lista de articulos seleccionados en el modal
    public $selected = [];

    #[Computed()]
    public function productosResult()
    {
        //Propiedad que almacena todos los items que coincidan con la busqueda.
        return CatalogoVistaVerde::where('nombre', 'like', '%' . $this->seachProduct . '%')
            ->whereNot('clave_dpto', 'RECEP')
            ->whereNot('estado', 0)
            ->orderBy('nombre', 'asc')
            ->limit(40)
            ->get();
    }

    public function finishSelect()
    {
        try {
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
                $producto = $this->productosResult->find($key);
                //Se anexa el producto al array de articulos
                $this->lista_articulos[] = [
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                ];
            }

            //Limpiamos las propiedades
            $this->selected = [];    //Productos seleccionados
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
        } catch (\Throwable $th) {
            dump($th->getMessage());
        }
    }

    public function searchRequisicion()
    {
        //Si hay folio de busqueda
        if ($this->folio_input) {
            //Buscar los detalles de la orden de compra (requisicion)
            $detalles_requi = DetallesCompra::where('folio_orden', $this->folio_input)->get();
            //Agregar al array los articulos
            foreach ($detalles_requi as $key => $articulo) {
                $this->lista_articulos[] = [
                    'codigo' => $articulo->codigo_producto,
                    'nombre' => $articulo->nombre,
                ];
            }
        }
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo(CatalogoVistaVerde $articulo)
    {
        $this->lista_articulos[] = [
            'codigo' => $articulo->codigo,
            'nombre' => $articulo->nombre,
        ];
    }

    public function removeItem($indexItem)
    {
        unset($this->lista_articulos[$indexItem]);
    }

    public function render()
    {
        return view('livewire.almacen.existencias.reporte');
    }
}

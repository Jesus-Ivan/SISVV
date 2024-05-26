<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoVistaVerde;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ProductosModalBody extends Component
{
    public $searchProduct;          //Almacenamos el campo de busqueda
    public $selectedProducts = [];  //Almacenamos los productos seleccionados [1 => false, 10 => true ....]

    #[Computed]
    public function productos()
    {
        if ($this->searchProduct != '') {
            //Reseteamos el array de productos seleccionados cada vez que se calcula la propiedad
            $this->reset('selectedProducts');
            //Obtenemos los productos que coincidan con el nombre buscado y del tipo de 'SER' = servicios
            return  DB::table('catalogo_vista_verde')
                ->join('tipos_catalogo', 'catalogo_vista_verde.codigo', '=', 'tipos_catalogo.codigo_catalogo')
                ->select('catalogo_vista_verde.*', 'tipos_catalogo.clave_tipo')
                ->where('nombre', 'like', '%' . $this->searchProduct . '%')
                ->where('clave_tipo', 'SER')
                ->get();
        } else {
            return [];
        }
    }

    //Metodo que se ejecuta para guardar los elementos seleccionados
    public function finalizarSeleccion()
    {
        //Filtramos los productos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->selectedProducts, function ($val) {
            return $val;
        });
        //Si ha seleccionado al menos 1 articulo
        if (count($total_seleccionados) > 0) {
            //Emitimos evento con los productos seleccionados, al resto de componentes
            $this->dispatch('productosSeleccionados', $total_seleccionados);
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
            //Reseteamos el componente
            $this->reset();
        }
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-modal-body');
    }
}

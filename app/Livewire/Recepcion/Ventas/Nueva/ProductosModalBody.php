<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoProducto;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductosModalBody extends Component
{
    public $searchProduct;          //Almacenamos el campo de busqueda
    public $selectedProducts = [];  //Almacenamos los productos seleccionados [1 => false, 10 => true ....]

    #[Computed]
    public function productos()
    {
        //Reseteamos el array de productos seleccionados cada vez que se calcula la propiedad
        $this->reset('selectedProducts'); 
        //Obtenemos los productos que coincidan con el nombre buscado
        return CatalogoProducto::where('nombre', 'like', '%' . $this->searchProduct . '%')->take(10)->get();
    }

    //Metodo que se ejecuta para guardar los elementos seleccionados
    public function finalizarSeleccion()
    {
        //Filtramos los productos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->selectedProducts, function ($val) {
            return $val;
        });
        //Emitimos evento con los productos seleccionados, al resto de componentes
        $this->dispatch('productosSeleccionados', $total_seleccionados);
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-modal-body');
    }
}

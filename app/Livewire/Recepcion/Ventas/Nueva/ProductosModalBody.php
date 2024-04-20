<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoProducto;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductosModalBody extends Component
{
    public $searchProduct;

    #[Computed]
    public function productos()
    {
        return CatalogoProducto::where('nombre', 'like', '%' . $this->searchProduct . '%')->take(8)->get();
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-modal-body');
    }
}

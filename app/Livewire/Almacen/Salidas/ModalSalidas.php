<?php

namespace App\Livewire\Almacen\Salidas;

use App\Constants\AlmacenConstants;
use App\Models\CatalogoVistaVerde;
use App\Models\Stock;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSalidas extends Component
{
    #[Modelable]
    public $clave_stock_origen;

    #[Locked]
    public $articulo_seleccionado;
    #[Locked]
    public $cantidad_stock;
    #[Locked]
    public $peso_stock;

    public $cantidad_salida;
    public $peso_salida;

    #[On('selected-articulo')]
    public function onSelectedInput(CatalogoVistaVerde $data)
    {
        $this->articulo_seleccionado = $data->toArray();
        $this->cantidad_stock = Stock::where('codigo_catalogo', $data->codigo)
            ->where("tipo", AlmacenConstants::CANTIDAD_KEY)
            ->first();
        $this->peso_stock =  Stock::where('codigo_catalogo', $data->codigo)
            ->where("tipo", AlmacenConstants::PESO_KEY)
            ->first();
    }

    public function agregarSalida()
    {
        //Emitimos evento para agregar la salida
        $this->dispatch('añadirSalida', [
            'codigo' => $this->articulo_seleccionado['codigo'],
            'nombre' => $this->articulo_seleccionado['nombre'],
            'cantidad_salida' => $this->cantidad_salida,
            'peso_salida' => $this->peso_salida
        ]);
        $this->dispatch('close-modal'); //Emitimos evento para cerrar el componente del modal
        $this->reset('articulo_seleccionado', 'cantidad_stock', 'peso_stock', 'cantidad_salida', 'peso_salida');
    }

    public function render()
    {
        return view('livewire.almacen.salidas.modal-salidas');
    }
}

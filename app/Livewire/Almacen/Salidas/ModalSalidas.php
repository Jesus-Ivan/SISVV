<?php

namespace App\Livewire\Almacen\Salidas;

use App\Constants\AlmacenConstants;
use App\Models\CatalogoVistaVerde;
use App\Models\Stock;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSalidas extends Component
{
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
        $validation_rules = [
            'cantidad' => 'required|numeric|min:1'
        ];
        $validated = $this->validate($validation_rules);
        //Emitimos evento para agregar la salida
        $this->dispatch('añadirSalida', [
            'codigo' => $this->articulo_seleccionado['codigo'],
            'nombre' => $this->articulo_seleccionado['nombre'],
            'stock' => $this->articulo_seleccionado['stock'],
            'cantidad' => $validated['cantidad']
        ]);
        $this->dispatch('close-modal'); //Emitimos evento para cerrar el componente del modal
        $this->reset();
    }

    public function render()
    {
        return view('livewire.almacen.salidas.modal-salidas');
    }
}

<?php

namespace App\Livewire\Almacen\Salidas;

use App\Models\InventarioPrincipal;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSalidas extends Component
{
    public $articuloSeleccionado;
    public $cantidad;

    #[On('on-selected-articulo')]
    public function onSelectedInput(InventarioPrincipal $data)
    {
        $this->articuloSeleccionado = $data->toArray();
    }

    public function agregarSalida()
    {
        $validation_rules = [
            'cantidad' => 'required|numeric|min:1'
        ];
        $validated = $this->validate($validation_rules);
        //Emitimos evento para agregar la salida
        $this->dispatch('añadirSalida', [
            'codigo' => $this->articuloSeleccionado['codigo'],
            'nombre' => $this->articuloSeleccionado['nombre'],
            'stock' => $this->articuloSeleccionado['stock'],
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

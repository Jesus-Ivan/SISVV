<?php

namespace App\Livewire\Puntos\Ventas\Nueva;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Container extends Component
{
    public VentaForm $ventaForm;

    #[On('on-selected-socio')]
    public function socioSeleccionado(Socio $socio)
    {
        $this->ventaForm->socioSeleccionado = $socio;
    }

    #[Computed()]
    public function productosResult()
    {
        return CatalogoVistaVerde::where('nombre', 'like', '%' . $this->ventaForm->seachProduct . '%')->limit(30)->get();
    }

    public function finishSelect()
    {
        try {
            //Intentamos guardar los items seleccionados, en la tabla
            $this->ventaForm->agregarItems($this->productosResult);
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
        } catch (\Throwable $th) {
            dump($th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.puntos.ventas.nueva.container');
    }
}

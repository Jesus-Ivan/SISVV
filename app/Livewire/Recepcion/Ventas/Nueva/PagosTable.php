<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class PagosTable extends Component
{
    #[Modelable]
    public $pagos = [];     //Almacenamos los pagos de forma temporal

    #[Computed()]
    public function total(){
        return array_sum(array_column($this->pagos, 'monto'));
    }
    
    #[On('onFinisPago')]
    public function agregar($pago)
    {
        $this->pagos[] = $pago;
    }

    #[On('get-datos')]
    public function sendData(){
        $this->dispatch('on-get-pagos',$this->pagos);
    }
    

    public function remove($pagoIndex)
    {
        //Eliminar el producto del array de pagos
        unset($this->pagos[$pagoIndex]);
    }
    
    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.pagos-table');
    }
}

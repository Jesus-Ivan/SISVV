<?php

namespace App\Livewire\Recepcion\Ventas;

use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Principal extends Component
{
    #[Computed()]
    public function ventasHoy()
    {
        //return Venta::whereDate('fecha_apertura', now()->format('Y-m-d'))->get();
        return Venta::all();
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.principal');
    }
}

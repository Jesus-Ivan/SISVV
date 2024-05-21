<?php

namespace App\Livewire\Recepcion\Ventas;

use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    #[Computed()]
    public function ventasHoy()
    {
        //return Venta::whereDate('fecha_apertura', now()->format('Y-m-d'))->get();
        return Venta::orderby('fecha_apertura','desc')->paginate(10);
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.principal');
    }
}

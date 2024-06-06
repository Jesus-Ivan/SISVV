<?php

namespace App\Livewire\Recepcion\Ventas;

use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;
    public $fecha;

    public function mount(){
        $this->fecha = now()->toDateString();
    }

    public function buscar (){
        $this->resetPage();
    }


    #[Computed()]
    public function ventasHoy()
    {
        return Venta::whereDate('fecha_apertura',$this->fecha)
            ->whereAny(['id_socio','nombre'], 'like', '%' . $this->search . '%')
            ->orderby('fecha_apertura', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.principal');
    }
}

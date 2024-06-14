<?php

namespace App\Livewire\Recepcion\Cobros;

use App\Models\Recibo;
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
    public function recibos()
    {
        return Recibo::whereDate('created_at',$this->fecha)
            ->whereAny(['id_socio','nombre'], 'like', '%' . $this->search . '%')
            ->orderby('created_at', 'desc')
            ->paginate(10);
    }
    public function render()
    {
        return view('livewire.recepcion.cobros.principal');
    }
}

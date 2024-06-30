<?php

namespace App\Livewire\Puntos\Ventas;

use App\Models\PuntoVenta;
use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    public $codigopv;
    public $search;
    public $fecha;

    public function mount()
    {
        $this->fecha = now()->toDateString();
    }
    
    #[Computed()]
    public function ventasHoy()
    {
        return Venta::whereAny(['id_socio', 'nombre'], 'like', '%' . $this->search . '%')
            ->whereDate('fecha_apertura', $this->fecha)
            ->where('clave_punto_venta', $this->codigopv)
            ->orderby('fecha_apertura', 'desc')
            ->paginate(10);
    }

    public function refresh()
    {
        //Resetear el paginador cada vez que se busca algo
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.puntos.ventas.principal');
    }
}

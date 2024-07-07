<?php

namespace App\Livewire\Puntos\Ventas;

use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PrincipalMesero extends Component
{
    use WithPagination;

    public $search = '';
    public $codigopv;

    #[Computed()]
    public function ventasHoy()
    {
        return Venta::whereAny(['id_socio', 'nombre'], 'like', '%' . $this->search . '%')
            ->whereDate('fecha_apertura', now()->toDateString())
            ->where('clave_punto_venta', $this->codigopv)
            ->whereNull('fecha_cierre')
            ->orderby('fecha_apertura', 'desc')
            ->paginate(10);
    }

    public function buscar()
    {
        //Resetear el paginador cada vez que se busca algo
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.puntos.ventas.principal-mesero');
    }
}

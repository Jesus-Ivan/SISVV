<?php

namespace App\Livewire\Puntos\Inventario;

use App\Constants\AlmacenConstants;
use App\Models\CatalogoVistaVerde;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class VerInventario extends Component
{
    use WithPagination;
    //Recibe el codigo del punto de venta, desde el exterior del componente (controlador)
    public $codigopv;

    public $search_input;
    public  $departamento = [
        'PV' => true,
        'ALM' => false
    ];

    #[Computed()]
    public function productos()
    {
        $dpto = array_filter($this->departamento, function ($d) {
            return $d;
        });
        $result = CatalogoVistaVerde::with('stocks', 'familia', 'categoria')
            ->whereAny(['codigo', 'nombre'], 'like', '%' . $this->search_input . '%')
            ->whereIn('clave_dpto', array_keys($dpto))
            ->where('estado', true)
            ->orderBy('nombre', 'asc')
            ->paginate(20);

        return $result;
    }

    public function buscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view(
            'livewire.puntos.inventario.ver-inventario',
            ['clave_stock' => AlmacenConstants::PUNTOS_STOCK]
        );
    }
}

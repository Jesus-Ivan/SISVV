<?php

namespace App\Livewire\Puntos\Inventario;

use App\Livewire\Concerns\ManejaSolicitudInventario;
use Livewire\Component;

class NuevaSolicitud extends Component
{
    use ManejaSolicitudInventario;

    public $hoy;
    public $searchInsumo = '';
    public $selectedItems = [];
    public $lista_productos = [];
    public $selectedGrupo = null;

    public $codigopv;
    public $permisospv;

    public function mount($codigopv, $permisospv)
    {
        $this->hoy = now();
        $this->codigopv = $codigopv;
        $this->permisospv = $permisospv;
    }

    protected function claveBodega(): string
    {
        return 'stock_' . strtolower($this->codigopv);
    }

    public function render()
    {
        return view('livewire.puntos.inventario.nueva-solicitud');
    }
}

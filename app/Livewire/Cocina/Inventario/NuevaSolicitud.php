<?php

namespace App\Livewire\Cocina\Inventario;

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

    public function mount()
    {
        $this->hoy = now();
    }

    protected function claveBodega(): string
    {
        return 'stock_coc';
    }

    public function render()
    {
        return view('livewire.cocina.inventario.nueva-solicitud');
    }
}

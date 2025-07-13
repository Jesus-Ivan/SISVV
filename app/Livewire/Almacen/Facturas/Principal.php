<?php

namespace App\Livewire\Almacen\Facturas;

use App\Models\DetallesFacturas;
use App\Models\Facturas;
use App\Models\Proveedor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search_mes;
    public $search_proveedor;

    //Detalles de las facturas en el modal
    #[Locked]
    public $factura_seleccionada, $factura ,$factura_detalles = [];

    // Inicializar propiedades al cargar el componente
    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    public function search_mes()
    {
        $this->resetPage();
    }

    public function search_proveedor()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function facturas()
    {
        $query = Facturas::query();

        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_compra', $year)
                ->whereMonth('fecha_compra', $month);
        }
        if ($this->search_proveedor) {
            $query->where('id_proveedor', $this->search_proveedor);
        }

        return $query->paginate(10);
    }

    public function detallesFact($folio)
    {
        $this->factura_seleccionada = $folio;
        $this->factura = Facturas::where('folio', $folio)->first();
        $this->factura_detalles = DetallesFacturas::where('folio_compra', $folio)->get();
        $this->dispatch('open-modal', name: 'detalles');
    }

    public function render()
    {
        return view('livewire.almacen.facturas.principal');
    }
}

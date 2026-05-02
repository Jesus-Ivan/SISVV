<?php

namespace App\Livewire\Administracion\Comprobaciones;

use App\Models\DetallesComprobaciones;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleComprobaciones extends Component
{
    public $detalles_seleccionado;
    public $search_area = '';
    public $search_concepto = '';

    public function mount($folio = null)
    {
        $this->detalles_seleccionado = $folio;
    }

    //Busqueda de gastos por area
    #[Computed()]
    public function areas()
    {
        return DetallesComprobaciones::where('folio_periodo', $this->detalles_seleccionado)
            ->distinct()
            ->pluck('area');
    }

    //Busqueada de gastos por concepto
    #[Computed()]
    public function conceptos()
    {
        return DetallesComprobaciones::where('folio_periodo', $this->detalles_seleccionado)
            ->distinct()
            ->pluck('concepto');
    }

    #[Computed()]
    public function detallesC()
    {
        if (!$this->detalles_seleccionado) {
            return collect();
        }

        //Iniciamos la consulta en la base de datos
        $query = DetallesComprobaciones::where('folio_periodo', $this->detalles_seleccionado)->orderBy('fecha_nota', 'asc');

        //Filtro de busqueda por area
        if ($this->search_area) {
            $query->where('area', $this->search_area);
        }

        //Filtro de busqueda por concepto
        if ($this->search_concepto) {
            $query->where('concepto', $this->search_concepto);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.administracion.comprobaciones.detalle-comprobaciones');
    }
}

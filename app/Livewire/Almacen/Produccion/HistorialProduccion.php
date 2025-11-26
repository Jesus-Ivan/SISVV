<?php

namespace App\Livewire\Almacen\Produccion;

use App\Models\DetalleTransformacion;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialProduccion extends Component
{
    use WithPagination;
    public $mes_busqueda, $search = '';

    public function mount()
    {
        $this->mes_busqueda = now()->format('Y-m');
    }

    public function actualizar()
    {
        $this->resetPage();
    }
    #[Computed()]
    public function producciones()
    {
        //Definir las columnas de la consulta
        $columns = [
            'folio_transformacion',
            'clave_insumo_elaborado',
            'cantidad',
            'rendimiento',
            'total_elaborado'
        ];
        $resultados = DetalleTransformacion::query()
            ->with('insumoElaborado', 'transformacion.origen', 'transformacion.destino')
            ->select($columns)
            ->where('clave_insumo_elaborado', 'like', '%' . $this->search . '%')
            ->groupBy($columns);
        return $resultados->paginate(10);
    }
    public function render()
    {
        return view('livewire.almacen.produccion.historial-produccion');
    }
}

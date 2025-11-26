<?php

namespace App\Livewire\Almacen\Produccion;

use App\Models\DetalleTransformacion;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialProduccion extends Component
{
    use WithPagination;
    public $mes_busqueda;
    public $selected_insumo = null;

    public function mount()
    {
        $this->mes_busqueda = now()->format('Y-m');
    }

    public function buscar()
    {
        $this->resetPage();
        $this->reset('selected_insumo');
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        $this->selected_insumo = $clave;
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

        //Parsear la fecha seleccionada
        $fecha = Carbon::parse($this->mes_busqueda);
        //Definir consulta base
        $resultados = DetalleTransformacion::query()
            ->with('insumoElaborado', 'transformacion.origen', 'transformacion.destino')
            ->whereHas('transformacion', function ($query) use ($fecha) {
                $query->whereMonth('fecha_existencias', $fecha->month)
                    ->whereYear('fecha_existencias', $fecha->year);
            })
            ->select($columns)
            ->groupBy($columns);

        //Si hay seleccionado un insumo, agregrar la condicion extra
        if ($this->selected_insumo) {
            $resultados->where('clave_insumo_elaborado', $this->selected_insumo);
        }

        return $resultados->paginate(15);
    }
    public function render()
    {
        return view('livewire.almacen.produccion.historial-produccion');
    }
}

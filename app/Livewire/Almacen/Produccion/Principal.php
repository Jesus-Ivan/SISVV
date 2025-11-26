<?php

namespace App\Livewire\Almacen\Produccion;

use App\Models\DetalleTransformacion;
use App\Models\Transformacion;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $mes_busqueda;
    public $folio;

    public function mount()
    {
        $this->mes_busqueda = now()->format('Y-m');
    }

    #[Computed()]
    public function producciones()
    {
        //Preparar el constructor de consultas
        $query = Transformacion::query();
        //Verificar si hay mes en la propiedad
        if ($this->mes_busqueda) {
            $year = substr($this->mes_busqueda, 0, 4);
            $month = substr($this->mes_busqueda, 5, 2);
            //Agregar condiciones a la consulta
            $query->with('origen', 'destino')
                ->whereYear('fecha_existencias', $year)
                ->whereMonth('fecha_existencias', $month);
        }
        return $query->orderBy('folio', "DESC")->paginate(10);
    }

    /**
     * Se ejecuta para buscar las ordenes.
     */
    public function buscar()
    {
        //Reinicia el paginador.
        $this->resetPage();
    }

    #[Computed()]
    public function detalles_produccion()
    {
        $resultados = DetalleTransformacion::query()
            ->with('insumoElaborado', 'transformacion.origen', 'transformacion.destino')
            ->distinct()
            ->select([
                'detalles_transformacion.folio_transformacion',
                'detalles_transformacion.clave_insumo_elaborado',
                'detalles_transformacion.cantidad',
                'detalles_transformacion.rendimiento',
                'detalles_transformacion.total_elaborado',
            ])
            ->where('folio_transformacion', $this->folio)
            ->get();
        return $resultados;
    }

    public function verDetalles($folio)
    {
        $this->folio = $folio;
        //Emitir evento para abrir el modal
        $this->dispatch('open-modal', name: 'modal-produccion');
    }

    public function render()
    {
        return view('livewire.almacen.produccion.principal');
    }
}

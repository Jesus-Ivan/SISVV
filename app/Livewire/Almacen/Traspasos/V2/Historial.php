<?php

namespace App\Livewire\Almacen\Traspasos\V2;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\DetalleTraspasoNew;
use App\Models\TraspasoNew;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Historial extends Component
{
    use WithPagination;
    public $search_mes;
    public $clave_origen = '', $clave_destino = '';
    public $primary = '';

    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo($primary)
    {
        $this->primary = $primary;
    }

    #[Computed()]
    public function traspasos()
    {
        $mes = Carbon::parse($this->search_mes);
        $detalles = DetalleTraspasoNew::with('traspaso')
            ->whereHas('traspaso', function ($query) use ($mes) {
                $query->whereMonth('fecha_existencias', $mes->month)
                    ->whereYear('fecha_existencias', $mes->year);


                if (!empty($this->clave_origen)) {
                    $query->where('clave_origen', $this->clave_origen);
                }

                if (!empty($this->clave_destino)) {
                    $query->where('clave_destino', $this->clave_destino);
                }
            });

        if (strlen($this->primary) > 0) {
            $detalles->whereAny(['clave_presentacion', 'clave_insumo'], $this->primary);
        }
        $detalles->orderBy(
            TraspasoNew::select('fecha_existencias')
                ->whereColumn("traspasos_new.folio", "detalle_traspaso_new.folio_traspaso")
                ->limit(1),
            "DESC"
        );

        return $detalles->paginate(10);
    }

    public function buscar()
    {
        //Limpiar la clave primaria seleccionada
        $this->reset('primary');
        //Resetar el paginador
        $this->resetPage();
    }

    public function render()
    {
        $bodega = Bodega::find($this->clave_origen);
        $tipo_articulo = false;

        if ($bodega) {
            if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                $tipo_articulo = true;
            } elseif ($bodega->naturaleza == AlmacenConstants::INSUMOS_KEY) {
                $tipo_articulo = false;
            }
        }

        return view('livewire.almacen.traspasos.v2.historial', [
            'tipo_articulo' => $tipo_articulo
        ]);
    }
}

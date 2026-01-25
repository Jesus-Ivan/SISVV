<?php

namespace App\Livewire\Almacen\Entradas\V2;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\DetalleEntradaNew;
use App\Models\EntradaNew;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Historial extends Component
{
    use WithPagination;
    public $clave_bodega = '', $mes_busqueda;
    public $primary = '';

    public function updated($prop, $val)
    {
        switch ($prop) {
            case 'clave_bodega':
                $this->buscar();
                break;
            case 'mes_busqueda':
                $this->buscar();
                break;
        }
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo($primary)
    {
        $this->primary = $primary;
    }

    public function mount()
    {
        $this->mes_busqueda = now()->format('Y-m');
    }

    #[Computed()]
    public function entradas()
    {
        //Parsear la fecha obtenida
        $mes = Carbon::parse($this->mes_busqueda);
        //Si la longitud de la clave primaria es mayor a cero
        if (strlen($this->primary) > 0) {
            //Preparar la consulta
            $detalles = DetalleEntradaNew::with(['proveedor', 'entrada'])
                ->whereHas('entrada', function ($query) use ($mes) {
                    $query->where('clave_bodega', $this->clave_bodega)
                        ->whereMonth('fecha_existencias', $mes->month)
                        ->whereYear('fecha_existencias', $mes->year);
                })
                ->whereAny(['clave_presentacion', 'clave_insumo'],  $this->primary) //Agregar condicion de busqueda para la clave primaria seleccionada
                ->orderBy(
                    EntradaNew::select('fecha_existencias')
                        ->whereColumn('entradas_new.folio', 'detalle_entrada_new.folio_entrada') // Relación
                        ->limit(1),
                    'DESC'
                )
                ->paginate(10);
        } else {
            //Preparar la consulta 
            $detalles = DetalleEntradaNew::with(['proveedor', 'entrada'])
                ->whereHas('entrada', function ($query) use ($mes) {
                    $query->where('clave_bodega', $this->clave_bodega)
                        ->whereMonth('fecha_existencias', $mes->month)
                        ->whereYear('fecha_existencias', $mes->year);
                })
                ->orderBy(
                    EntradaNew::select('fecha_existencias')
                        ->whereColumn('entradas_new.folio', 'detalle_entrada_new.folio_entrada') // Relación
                        ->limit(1),
                    'DESC'
                )
                ->paginate(10);
        }

        return $detalles;
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    /**
     * Limpia la clave primaria seleccionada y resetea el paginador
     */
    public function buscar()
    {
        //Limpiar la clave primaria seleccionada
        $this->reset('primary');
        //Resetar el paginador
        $this->resetPage();
    }
    public function render()
    {
        //Buscar la bodega seleccionada
        $bodega = Bodega::find($this->clave_bodega);
        //Definir variable auxiliar
        $is_presentacion = false;

        //Si se encontro la bodega
        if ($bodega) {
            //Si la naturaleza de la bodega es de presentaciones
            if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                $is_presentacion = true;  //Actualizar el valor booleano 
            } elseif ($bodega->naturaleza == AlmacenConstants::INSUMOS_KEY) {
                $is_presentacion = false;
            }
        }
        return view('livewire.almacen.entradas.v2.historial', [
            'is_presentacion' => $is_presentacion
        ]);
    }
}

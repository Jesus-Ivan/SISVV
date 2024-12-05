<?php

namespace App\Livewire\Almacen\Traspasos;

use App\Models\Bodega;
use App\Models\DetallesTraspaso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Historial extends Component
{
    use WithPagination;
    public $fInicio, $fFin, $selected_codigo;

    public function mount()
    {
        $this->fInicio = now()->day(1)->toDateString();
        $this->fFin = now()->day(now()->daysInMonth)->toDateString();
    }

    #[Computed()]
    public function detalles()
    {
        if ($this->selected_codigo) {
            return DB::table('detalles_traspasos')
                ->join('traspasos', 'detalles_traspasos.folio_traspaso', '=', 'traspasos.folio')
                ->where('codigo_articulo', $this->selected_codigo)
                ->whereDate('created_at', '>=', $this->fInicio)
                ->whereDate('created_at', '<=', $this->fFin)
                ->orderBy('id', "DESC")
                ->paginate(20);
        } else {
            return DB::table('detalles_traspasos')
                ->join('traspasos', 'detalles_traspasos.folio_traspaso', '=', 'traspasos.folio')
                ->whereDate('created_at', '>=', $this->fInicio)
                ->whereDate('created_at', '<=', $this->fFin)
                ->orderBy('id', "DESC")
                ->paginate(20);
        }
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo($codigo)
    {
        //Revisar las fechas
        $this->checkDates();
        //Guardar el codigo del articulo
        $this->selected_codigo = $codigo;
    }

    public function buscar()
    {
        //Revisar las fechas
        $this->checkDates();
        //reiniciar el paginador
        $this->resetPage();
        //limpiar el codigo selecciondo
        $this->reset('selected_codigo');
    }

    private function checkDates()
    {
        //Crear las fechas de tipo carbon
        $fecha_inicio = Carbon::parse($this->fInicio);
        $fecha_fin = Carbon::parse($this->fFin);
        //Obtenemos la diferencia de los dias
        $dias = $fecha_inicio->diffInDays($fecha_fin) + 1;
        //si excede el rango maximo
        if ($dias > 31) {
            //mensaje de sesion
            session()->flash('fail-busqueda', 'Rango maximo de dias: 31');
            //emitir evento
            $this->dispatch('busqueda');
            //corregir el dia maximo en la fecha de fin
            $this->fFin = $fecha_inicio->addDays(30)->toDateString();
        }
    }

    public function render()
    {
        return view('livewire.almacen.traspasos.historial', ["bodegas" => Bodega::all()]);
    }
}

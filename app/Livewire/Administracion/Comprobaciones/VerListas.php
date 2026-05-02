<?php

namespace App\Livewire\Administracion\Comprobaciones;

use App\Models\DetallesComprobaciones;
use App\Models\PeriodoComprobaciones;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class VerListas extends Component
{
    use WithPagination;
    public $search_mes;

    // Inicializar propiedades al cargar el componente
    public function mount()
    {
        $this->search_mes = now()->format('Y-m');
    }

    #[Computed()]
    public function comprobaciones()
    {
        $query = PeriodoComprobaciones::query();
        
        //Agregamos la sumatoria de importe para mostrarla en la tabla
        $query->withSum('detalles as total_importe', 'importe');

        if ($this->search_mes) {
            $year = substr($this->search_mes, 0, 4);
            $month = substr($this->search_mes, 5, 2);
            $query->whereYear('fecha_inicio', $year)
                ->whereMonth('fecha_inicio', $month)
                ->orderBy('fecha_inicio', 'asc');
        }
        return $query->paginate(10);
    }

    public function deleteComprobacion($comprob)
    {
        try {
            DB::transaction(function () use ($comprob) {
               PeriodoComprobaciones::where('folio', $comprob)->delete();
               DetallesComprobaciones::where('folio_periodo', $comprob)->delete();
            });
            session()->flash('success', "Registro eliminado correctamente");
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    public function render()
    {
        return view('livewire.administracion.comprobaciones.ver-listas');
    }
}

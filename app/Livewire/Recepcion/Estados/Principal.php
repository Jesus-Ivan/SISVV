<?php

namespace App\Livewire\Recepcion\Estados;

use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;
    public $radioButon;
    public $vista;
    public bool $soloTarifaEspecial = false;

    public $fechaInicio, $fechaFin;

    //Iniciamos los valores por defecto
    public function mount()
    {   //Fecha del primer dia del mes actual
        $this->fechaInicio = '2020-01-01';
        //Fecha del ultimo dia del mes actual
        $this->fechaFin = now()->day(now()->daysInMonth)->toDateString();
        $this->search = '';                     //Nombre por default
        $this->radioButon = 'T';                //Radio button 'Mostrar Todos'
        $this->vista = 'COM';                   //Select 'Mostrar Todos'
    }

    #[Computed()]
    public function resultSocios()
    {
        $query = Socio::with(['socioMembresia', 'cuotasMembresia.cuota'])
            ->withExists(['socioCuotas as tiene_tarifa_especial' => fn($q) => $q->whereNotNull('monto_personalizado')])
            ->where(function ($q) {
                $q->whereAny(['nombre', 'apellido_p', 'apellido_m'], 'LIKE', '%' . $this->search . '%')
                    ->orWhere('id', $this->search);
            });

        if ($this->soloTarifaEspecial) {
            $query->whereHas('socioCuotas', fn($q) => $q->whereNotNull('monto_personalizado'));
        }

        return $query->paginate(5);
    }

    //hook que se ejecuta cuando una propiedad se actualizo
    public function updated($property, $value)
    {
        // Resetear paginacion al cambiar cualquier filtro
        $this->resetPage();

        if ($property == 'fechaInicio' && $value === '') {
            $this->fechaInicio = now()->day(1)->toDateString();
        }
        if ($property == 'fechaFin' && $value === '') {
            $this->fechaFin = now()->day(now()->daysInMonth)->toDateString();
        }
    }

    public function toggleTarifaEspecial()
    {
        $this->soloTarifaEspecial = !$this->soloTarifaEspecial;
        $this->resetPage();
    }

    public function setConceptos($valor)
    {
        $this->radioButon = $valor;
        $this->resetPage();
    }

    public function search()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.recepcion.estados.principal');
    }
}

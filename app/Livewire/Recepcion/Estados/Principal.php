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

    public $fechaInicio, $fechaFin;

    //Iniciamos los valores por defecto
    public function mount()
    {   //Fecha del primer dia del mes actual
        $this->fechaInicio = now()->day(1)->toDateString();
        //Fecha del ultimo dia del mes actual
        $this->fechaFin = now()->day(now()->daysInMonth)->toDateString();
        $this->search = '';                     //Nombre por default
        $this->radioButon = 'T';                //Radio button 'Mostrar Todos'
    }

    #[Computed()]
    public function resultSocios()
    {
        return Socio::with('socioMembresia')->whereAny([
            'nombre',
            'apellido_p',
            'apellido_m',
        ], 'LIKE', '%' . $this->search . '%')
            ->orWhere('id', $this->search)
            ->limit(40)
            ->paginate(5);
    }

    public function buscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.recepcion.estados.principal');
    }
}

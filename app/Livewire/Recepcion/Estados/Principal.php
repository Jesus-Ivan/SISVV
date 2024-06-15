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

    public $fechaInicio, $fechaFin;

    //Iniciamos los valores por defecto
    public function mount()
    {   //Fecha del primer dia del mes actual
        $this->fechaInicio = now()->day(1)->toDateString();
        //Fecha del ultimo dia del mes actual
        $this->fechaFin = now()->day(now()->daysInMonth)->toDateString();
        $this->search = '';                     //Nombre por default
        $this->radioButon = 'T';                //Radio button 'Mostrar Todos'
        $this->vista = 'COM';                     //Select 'Mostrar Todos'
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
            ->limit(30)
            ->paginate(5);
    }

    //hook que se ejecuta cuando una propiedad se actualizo
    public function updated($property, $value)
    {
        if ($property == 'fechaInicio') {
            //Si es cadena vacia, reseteamos
            if ($value =="") {
                //dump($property,$value);
                $this->fechaInicio = now()->day(1)->toDateString();
            }
        }
        if ($property == 'fechaFin') {
            //Si la fecha de fin es cadena vacia, reseteamos
            if ($value === "") {
                $this->fechaFin = now()->day(now()->daysInMonth)->toDateString();
            }
        }
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

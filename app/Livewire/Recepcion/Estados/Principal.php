<?php

namespace App\Livewire\Recepcion\Estados;

use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    public $year;
    public $month;
    public $search;
    public $radioButon;

    //Iniciamos los valores por defecto
    public function mount()
    {   //Fecha actual
        $this->year = now()->year;
        $this->month = now()->month;              
        $this->search = '';                     //Nombre por default
        $this->radioButon = 'T';                //Radio button 'Mostrar Todos'
    }

    #[Computed()]
    public function resultSocios()
    {
        return Socio::with('membresia')->where('nombre', 'LIKE', '%' . $this->search . '%')
            ->orWhere('id', $this->search)
            ->limit(20)
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

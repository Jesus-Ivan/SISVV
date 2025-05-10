<?php

namespace App\Livewire\Acceso\Socios;

use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Principal extends Component
{
    public ?Socio $socio = null;
    public $search;
    public $result = [];

    public function buscar()
    {
        $this->result = Socio::with('integrantesSocio', 'socioMembresia')
            ->where('id', $this->search)->get();
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.acceso.socios.principal');
    }
}

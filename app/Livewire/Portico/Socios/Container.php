<?php

namespace App\Livewire\Portico\Socios;

use App\Models\IntegrantesSocio;
use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Container extends Component
{
    public ?Socio $socio = null;

    #[On('on-selected-socio')]
    public function selectedSocio($socio)
    {
        $this->socio = Socio::with(['integrantesSocio', 'socioMembresia'])->find($socio);
    }

    public function render()
    {
        return view('livewire.portico.socios.container');
    }
}

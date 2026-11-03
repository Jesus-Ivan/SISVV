<?php

namespace App\Livewire\Administracion\Nomina;

use Livewire\Attributes\Url;
use Livewire\Component;

class Principal extends Component
{
    #[Url]
    public string $tab = 'dispersion'; // 'dispersion', 'empleados', 'cuentas', 'catalogos'

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.administracion.nomina.principal')
            ->layout('layouts.app', ['title' => 'Control de Nómina - Vista Verde Country Club']);
    }
}


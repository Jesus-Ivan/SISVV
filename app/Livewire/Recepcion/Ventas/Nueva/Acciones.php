<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use Livewire\Attributes\On;
use Livewire\Component;

class Acciones extends Component
{
    public  $datosSocio;
    public  $datosProductos;
    public  $datosPagos;

    #[On('on-get-socio')]
    public function establecerDatosSocio($data)
    {
        $this->datosSocio = $data;
    }

    #[On('on-get-productos')]
    public function establecerDatosProductos($data)
    {
        $this->datosProductos = $data;
    }

    #[On('on-get-pagos')]
    public function establecerDatosPagos($data)
    {
        $this->datosPagos = $data;
    }

    public function updated($datosSocio)
    {
        dump($this->datosSocio, $this->datosProductos, $this->datosPagos);
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.acciones');
    }
}

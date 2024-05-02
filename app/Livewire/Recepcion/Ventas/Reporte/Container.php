<?php

namespace App\Livewire\Recepcion\Ventas\Reporte;

use App\Models\Caja;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Container extends Component
{
    #[Validate('required')]
    public $fecha;

    public $ventas = [];

    #[Computed()]
    public function caja()
    {
        //Buscamos en la BD, el punto de venta de recepcion
        $result = PuntoVenta::where('nombre', 'like', '%RECEP%')->take(1)->get();
        //Si existe el punto de venta, buscamos la caja
        if (count($result) > 0) {
            //Buscamos la caja del usuario auntenticado, en la fecha especifica, en el punto actual
            return Caja::whereDate('fecha_apertura', $this->fecha)
                ->where('id_usuario', auth()->user()->id)
                ->where('clave_punto_venta',$result[0]->clave)
                ->take(1)
                ->get();
        } else {
            return [];
        }
    }

    public function buscar()
    {
        $this->validate();
        //Si existe un registro de caja, buscamos las ventas
        if (count($this->caja) > 0) {
            $this->ventas = Venta::where('corte_caja', $this->caja[0]->corte)->get();
        } else {
            //Si no hay caja, borramos la lista de ventas
            $this->ventas = [];
        }
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.reporte.container');
    }
}

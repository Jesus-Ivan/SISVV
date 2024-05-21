<?php

namespace App\Livewire\Recepcion\Cobros\Reporte;

use App\Models\Caja;
use App\Models\PuntoVenta;
use App\Models\Recibo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Container extends Component
{
    use WithPagination;

    #[Validate('required')]
    public $fecha;

    #[Locked]
    public $caja;

    public function buscar()
    {
        //Limpiamos el paginador
        $this->resetPage();
        //Validamos los datos
        $this->validate();
        //Buscamos en la BD, el punto de venta de recepcion
        $result = PuntoVenta::where('nombre', 'like', '%RECEP%')->take(1)->get();
        //Buscamos la caja, con el usuario correspondiente, en el punto.
        $resultCaja = Caja::whereDate('fecha_apertura', $this->fecha)
            ->where('id_usuario', auth()->user()->id)
            ->where('clave_punto_venta', $result[0]->clave)
            ->take(1)
            ->get();
        //Si existe un registro de caja
        if (count($resultCaja) > 0) {
            $this->caja = $resultCaja;
        } else {
            $this->caja = null;
        }
    }
    public function render()
    {
        //Si existe la caja, buscar los cobros asociados y devolver la vista con los datos
        if ($this->caja) {
            return view(
                'livewire.recepcion.cobros.reporte.container',
                [
                    'cobros' => Recibo::where('corte_caja', $this->caja[0]->corte)->paginate(10)
                ]
            );
        } else {
            //Devolver la vista, con array vacio
            return view(
                'livewire.recepcion.cobros.reporte.container',
                [
                    'cobros' => []
                ]
            );
        }
    }
}

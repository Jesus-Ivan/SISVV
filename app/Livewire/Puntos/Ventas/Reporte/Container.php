<?php

namespace App\Livewire\Puntos\Ventas\Reporte;

use App\Models\Caja;
use App\Models\Venta;
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

    #[Locked]
    public $codigopv;

    public function buscar()
    {
        //Limpiamos el paginador
        $this->resetPage();
        //Validamos los datos
        $this->validate();
        //Buscamos la caja, con el usuario correspondiente, en el punto.
        $resultCaja = Caja::whereDate('fecha_apertura', $this->fecha)
            ->where('id_usuario', auth()->user()->id)
            ->where('clave_punto_venta', $this->codigopv)
            ->first();
        //Si existe un registro de caja
        if ($resultCaja) {
            $this->caja = $resultCaja;
        } else {
            $this->caja = null;
        }
    }
    public function render()
    {
        if ($this->caja) {
            return view('livewire.puntos.ventas.reporte.container', [
                'ventas' => Venta::where('corte_caja', $this->caja->corte)->paginate(10)
            ]);
        } else {
            return view('livewire.puntos.ventas.reporte.container', [
                'ventas' => []
            ]);
        }
    }
}

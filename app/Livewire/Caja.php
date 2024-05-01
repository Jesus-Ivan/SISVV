<?php

namespace App\Livewire;

use App\Models\Caja as ModelsCaja;
use App\Models\PuntoVenta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Caja extends Component
{
    public $punto;

    //Buscamos la clave del punto correspondiente, proveniente como atributo externo
    public function mount($nombrePunto)
    {
        $result = PuntoVenta::where('nombre', 'LIKE', '%' . $nombrePunto . '%')
            ->limit(1)
            ->get();
        $this->punto = $result[0];
    }

    #[Computed]
    public function usuario()
    {
        return auth()->user();
    }

    #[Computed]
    public function statusCaja()
    {
        //Buscamos al menos 1 registro de caja abierta, con el usuario autenticado, en el punto actual.
        return ModelsCaja::where('fecha_cierre', null)
            ->where('id_usuario', $this->usuario->id)
            ->where('clave_punto_venta', $this->punto->clave)
            ->limit(1)
            ->get();
    }

    public function abrirCaja()
    {
        //Comprobamos si no hay cajas abiertas del usuario autenticado.
        if (count($this->statusCaja) == 0) {
            // Format without timezone offset
            $fechaApertura = now()->format('Y-m-d H:i:s');
            ModelsCaja::create([
                'fecha_apertura' => $fechaApertura,
                'id_usuario' => $this->usuario->id,
                'clave_punto_venta' => $this->punto->clave
            ]);
            $this->dispatch('cajaActualizada')->self();
        } else {
            session()->flash('fail', 'No se pudo abrir la caja');
            $this->dispatch('info-caja');
        }
    }

    public function cerrarCaja()
    {
        // Format without timezone offset
        $fechaCierre = now()->format('Y-m-d H:i:s');
        try {
            //Actualizamos el estatus de la caja actual.
            $this->statusCaja[0]->update(['fecha_cierre' => $fechaCierre]);
            $this->dispatch('cajaActualizada')->self();
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('info-caja');
        }
    }

    public function verCaja()
    {
        dump($this->punto);
    }

    #[On('cajaActualizada')]
    public function render()
    {
        return view('livewire.caja');
    }
}

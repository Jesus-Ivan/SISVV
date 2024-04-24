<?php

namespace App\Livewire;

use App\Models\Caja as ModelsCaja;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Caja extends Component
{
    #[Computed]
    public function usuario()
    {
        return auth()->user();
    }

    #[Computed]
    public function statusCaja()
    {
        return ModelsCaja::where('fecha_cierre', null)
            ->where('id_usuario', $this->usuario->id)
            ->take(1)
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
            $this->statusCaja[0]->update(['fecha_cierre' => $fechaCierre]);
            $this->dispatch('cajaActualizada')->self();
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('info-caja');
        }
    }

    #[On('cajaActualizada')]
    public function render()
    {
        return view('livewire.caja');
    }
}

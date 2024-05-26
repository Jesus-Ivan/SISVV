<?php

namespace App\Livewire;

use App\Models\Caja as ModelsCaja;
use App\Models\PuntoVenta;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Caja extends Component
{
    #[Validate('required')]
    public $puntoSeleccionado;

    #[Validate('required|numeric| min:1')]
    public $cambio;

    #[Computed]
    public function usuario()
    {
        return auth()->user();
    }

    #[Computed]
    public function puntos()
    {
        return PuntoVenta::all();
    }

    #[Computed]
    public function statusCaja()
    {
        $hoy = now();
        $ayer = now()->yesterday();
        //Buscamos al menos 1 registro de caja abierta, con el usuario autenticado, en el punto actual.
        return ModelsCaja::whereDate('fecha_apertura', '<=', $hoy->format('Y-m-d'))
            ->whereDate('fecha_apertura', '>=', $ayer->format('Y-m-d'))
            ->where('id_usuario', $this->usuario->id)
            ->get();
    }

    public function ver()
    {
        dump($this->statusCaja());
    }

    public function abrirCaja()
    {
        $validated = $this->validate();

        //Buscamos las cajas del usuario, en un punto determinado, en el dia actual.
        $resultCajaHoy = ModelsCaja::where('id_usuario', $this->usuario->id)
            ->whereDate('fecha_apertura', '=', now()->format('Y-m-d'))
            ->where('clave_punto_venta', $this->puntoSeleccionado)
            ->get();

        //Comprobamos si intenta abrir caja en el mismo dia
        if (count($resultCajaHoy) == 0) {
            // Format without timezone offset
            $fechaApertura = now()->format('Y-m-d H:i:s');
            ModelsCaja::create([
                'fecha_apertura' => $fechaApertura,
                'id_usuario' => $this->usuario->id,
                'cambio_inicial' => $validated['cambio'],
                'clave_punto_venta' => $validated['puntoSeleccionado']
            ]);
            session()->flash('success', 'Caja abierta correctamente');
            $this->reset();
        } else {
            session()->flash('fail', 'No se pudo abrir la caja');
        }
        $this->dispatch('info-caja');
    }

    public function cerrarCaja(ModelsCaja $caja)
    {
        // Format without timezone offset
        $fechaCierre = now()->format('Y-m-d H:i:s');
        try {
            //Verificamos si la caja ya tenia fecha de cierre
            if ($caja->fecha_cierre) {
                //lanzamos excepcion si ya esta cerrada la caja
                throw new Exception('La caja ya esta cerrada');
            }
            //Actualizamos el estatus de la caja actual si no hay errores.
            $caja->update(['fecha_cierre' => $fechaCierre]);
        } catch (\Throwable $th) {
            //Enviamos mensaje de sesion en livewire
            session()->flash('fail', $th->getMessage());
            //Emitimos evento para abrir alert
            $this->dispatch('info-caja');
        }
    }

    public function cierreParcial(ModelsCaja $caja)
    {
        // Format without timezone offset
        $fechaParcial = now()->format('Y-m-d H:i:s');
        try {
            //Verificamos si la caja ya tenia fecha de cierre
            if ($caja->fecha_cierre || $caja->cierre_parcial) {
                //lanzamos excepcion si ya esta cerrada la caja
                throw new Exception('No se puede actualizar la caja');
            }
            //Actualizamos el estatus de la caja actual si no hay errores.
            $caja->update(['cierre_parcial' => $fechaParcial]);
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('info-caja');
        }
    }

    public function render()
    {
        return view('livewire.caja');
    }
}

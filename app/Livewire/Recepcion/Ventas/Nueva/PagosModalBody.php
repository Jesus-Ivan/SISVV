<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use App\Models\TipoPago;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PagosModalBody extends Component
{
    public Socio $socio;
    public TipoPago $metodo_pago;
    public $pago, $monto, $propina;

    #[Computed]
    public function tiposPago()
    {
        return TipoPago::all();
    }

    public function finishPago()
    {
        $validation_rules = [
            'monto' => 'required|numeric|min:1',
            'pago' => 'required',
        ];
        //Validamos las entradas
        $validated = $this->validate($validation_rules);

        //Emitimos evento para agregar el pago
        //El par clave-valor ('descripcion_tipo_pago' => ''), se remueve antes de insertar en la base de datos
        $this->dispatch('onFinisPago', [
            'id_socio' => $this->socio->id,
            'nombre' => $this->socio->nombre,
            'monto' => $validated['monto'],
            'propina' => $this->propina,
            'id_tipo_pago' => $this->metodo_pago->id,
            'descripcion_tipo_pago' => $this->metodo_pago->descripcion,
        ]);
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
        $this->reset();
    }

    //Cada vez que el usuario cambia el valor del select
    //Buscamos en la BD, el tipo de pago correspondiente
    public function selectMetodo($value)
    {
        if ($value) {
            $this->metodo_pago = TipoPago::find($value);
        }
    }

    #[On('on-selected-socio-pago')]
    public function onSelectSocioPago(Socio $socio)
    {
        $this->socio = $socio;
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.pagos-modal-body');
    }
}

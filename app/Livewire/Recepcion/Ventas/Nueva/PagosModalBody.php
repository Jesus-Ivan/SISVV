<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use App\Models\TipoPago;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PagosModalBody extends Component
{
    public $socio = [];
    public TipoPago $metodo_pago;
    public $pago, $monto, $propina;
    public $invitado;

    #[Computed]
    public function tiposPago()
    {
        //Si es socio, mostrar metodo de pago de firma
        if (!$this->invitado) {
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'TRANSFERENCIA')
                    ->orWhere('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%');
            })->get();
        } else {
            //Retirar firma si es invitado
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'TRANSFERENCIA')
                    ->orWhere('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%')
                    ->orWhere('descripcion', 'like', 'FIRMA');
            })->get();
        }
    }

    public function finishPago()
    {
        $validation_rules = [
            'monto' => 'required|numeric',
            'pago' => 'required',
        ];
        //Validamos las entradas
        $validated = $this->validate($validation_rules);

        //Emitimos evento para agregar el pago
        //El par clave-valor ('descripcion_tipo_pago' => ''), se remueve antes de insertar en la base de datos
        $this->dispatch('onFinisPago', [
            'id_socio' => array_key_exists('id', $this->socio) ? $this->socio['id'] : null,
            'nombre' =>  array_key_exists('id', $this->socio) ? $this->socio['nombre'] . ' ' . $this->socio['apellido_p'] . ' ' . $this->socio['apellido_m'] : $this->socio['nombre'],
            'monto' => $validated['monto'],
            'propina' => $this->propina,
            'id_tipo_pago' => $this->metodo_pago->id,
            'descripcion_tipo_pago' => $this->metodo_pago->descripcion,
        ]);
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
        //Si no esta activada la opcion de invitado
        if (!$this->invitado) {
            $this->reset();
        } else {
            //Si esta activada la opcion de invitado, resetear solo el metodo de pago
            $this->reset('metodo_pago', 'pago', 'monto', 'propina');
        }
    }

    //Cada vez que el usuario cambia el valor del select
    //Buscamos en la BD, el tipo de pago correspondiente
    public function selectMetodo($value)
    {
        if ($value) {
            $this->metodo_pago =  TipoPago::find($value);
        }
    }

    #[On('on-selected-socio-pago')]
    public function onSelectSocioPago(Socio $socio)
    {
        $this->socio = $socio->toArray();
    }

    #[On('on-invitado')]
    public function onInvitado(bool $val, $invitado)
    {
        //guardamos el valor recibido del evento, en propiedad del componente, para efectuar la venta de tipo 'invitado'
        $this->invitado = $val;
        //Guardamos la informacion del invitado
        $this->socio = $invitado;
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.pagos-modal-body');
    }
}

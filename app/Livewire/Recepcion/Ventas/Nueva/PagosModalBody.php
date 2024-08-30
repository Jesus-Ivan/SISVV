<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\Socio;
use App\Models\SocioMembresia;
use App\Models\TipoPago;
use Exception;
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
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%');
            })->get();
        } else {
            //Retirar firma si es invitado
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%')
                    ->orWhere('descripcion', 'like', 'FIRMA');
            })->get();
        }
    }

    #[On('ver-ticket')]
    public function onVerTicket($venta)
    {
        //Si no esta activada la opcion de invitado
        if (!$this->invitado) {
            $this->reset();
        } else {
            //Si esta activada la opcion de invitado, resetear solo el metodo de pago
            $this->reset('metodo_pago', 'pago', 'monto', 'propina', 'socio');
        }
    }

    public function finishPago()
    {
        $validation_rules = [
            'monto' => 'required|numeric',
            'pago' => 'required',
            'socio.nombre' => 'required|min:3'
        ];

        //Validamos las entradas
        $validated = $this->validate($validation_rules);

        try {
            //Si no esta activada la opcion de invitado
            if (!$this->invitado)
                $this->validarFirma($this->socio['id']);        //Validar la firma del socio
        } catch (\Throwable $th) {
            session()->flash('fail_firma', $th->getMessage());
            return;
        }

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
        try {
            //Validamos si el socio no esta con una membresia cancelada
            $resultMembresia = SocioMembresia::where('id_socio', $socio->id)->first();
            if (!$resultMembresia) {
                throw new Exception("No se encontro membresia registrada");
            } else if ($resultMembresia->estado == 'CAN') {
                throw new Exception("Membresia de socio $socio->id cancelada");
            }
            $this->socio = $socio->toArray();
        } catch (\Throwable $th) {
            session()->flash('fail_socio',  $th->getMessage());
        }
    }

    #[On('on-invitado')]
    public function onInvitado(bool $val, $invitado)
    {
        //guardamos el valor recibido del evento, en propiedad del componente, para efectuar la venta de tipo 'invitado'
        $this->invitado = $val;
        //Guardamos la informacion del invitado
        $this->socio = $invitado;
    }

    private function validarFirma($socioId)
    {
        //Si de los metodos de pago, el actual seleccionado es firma.
        if ($this->tiposPago->where('descripcion', 'like', 'FIRMA')->first()->id == $this->metodo_pago->id) {
            //Buscamos el socio
            $result = Socio::find($socioId);
            //Si el socio no tiene firma
            if (!$result->firma) {
                throw new Exception("Este socio no tiene firma autorizada");
            }
        }
    }

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.pagos-modal-body');
    }
}

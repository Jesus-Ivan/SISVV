<?php

namespace App\Livewire\Recepcion\Estados;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\Socio;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CargosNuevo extends Component
{
    #[Locked]
    public $socio;
    #[Locked]
    public $year;
    #[Locked]
    public $month;

    public $search;
    public $listaCargos = [];

    //Propiedad computarizada que pobla los resultados de busqueda
    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')->limit(10)->get();
    }

    //Reglas de validacion para el campo de monto de los cargos
    public function rules()
    {
        return [
            'listaCargos.*.monto' => 'required|numeric|min:0.01', // Not zero, allow decimals
        ];
    }

    //Mensajes de error de validacion
    public function messages()
    {
        return [
            'listaCargos.*.monto.required' => 'El monto es obligatorio',
            'listaCargos.*.monto.numeric' => 'El monto debe ser un número',
            'listaCargos.*.monto.min' => 'El monto mínimo permitido es 0.01',
        ];
    }

    //Agrega el cargo de forma temporal al array de cargos
    public function addCuota($cuota)
    {
        $this->listaCargos[] = $cuota;
    }

    //Eliminar el cargo del array de cargos
    public function removeCuota($cargoIndex)
    {
        unset($this->listaCargos[$cargoIndex]);
    }

    public function guardarCambios()
    {
        //Validamos la lista de cargos no este vacia
        $this->validate(['listaCargos' => 'min:1']);

        foreach ($this->listaCargos as $cargoIndex => $cargo) {
            //Validamos el monto de cada uno de los cargos
            $this->validateOnly('listaCargos.' . $cargoIndex . '.monto', $this->rules());
        }
        //Creamos una fecha auxiliar de hoy
        $today = now();
        //Si la fecha no es del mes actual, entonces la fecha de la mensualidad es el primer dia del mes dado
        if (!($today->year == $this->year && $today->month == $this->month)) {
            $mensualidad = now()->day(1)->month($this->month)->year($this->year);
        } else {
            //Si la fecha es del mes actual, conservar fecha mensualidad igual a la actual
            $mensualidad = now();
        }
        try {
            //Creamos una transaccion para que si hay un error, no se guarden los datos
            DB::transaction(function () use ($mensualidad) {
                //Actualizamos el estado de cuenta
                foreach ($this->listaCargos as $key => $cargo) {
                    EstadoCuenta::create([
                        'clave_cuota' => $cargo['clave'],
                        'id_socio' => $this->socio->id,
                        'concepto' => $cargo['descripcion'] . ' ' . $this->getMes($mensualidad->month) . '-' . $mensualidad->year,
                        'fecha' => $mensualidad->format('Y-m-d'),
                        'cargo' => $cargo['monto'],
                        'saldo' => $cargo['monto'],
                    ]);
                }
            });
            //Emitimos evento para abrir alert
            $this->dispatch('action-message-cargos');
            //Enviamos mensaje de sesion
            session()->flash('success', 'Cargos registrados con éxito');
            //Limpiamos la lista
            $this->reset(['listaCargos']);
        } catch (Exception $e) {
            $this->dispatch('action-message-cargos');
            session()->flash('fail', $e->getMessage());
        }
    }
    //Recibe una numero del mes y devuelve el nombre del mes en español
    private function getMes($mes)
    {
        switch ($mes) {
            case 1:
                return 'Enero';
            case 2:
                return 'Febrero';
            case 3:
                return 'Marzo';
            case 4:
                return 'Abril';
            case 5:
                return 'Mayo';
            case 6:
                return 'Junio';
            case 7:
                return 'Julio';
            case 8:
                return 'Agosto';
            case 9:
                return 'Septiembre';
            case 10:
                return 'Octubre';
            case 11:
                return 'Noviembre';
            case 12:
                return 'Diciembre';
        }
    }
    public function render()
    {
        return view('livewire.recepcion.estados.cargos-nuevo', [
            'mes' => $this->getMes($this->month)
        ]);
    }
}

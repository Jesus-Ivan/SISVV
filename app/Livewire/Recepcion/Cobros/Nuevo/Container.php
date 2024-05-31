<?php

namespace App\Livewire\Recepcion\Cobros\Nuevo;

use App\Models\EstadoCuenta;
use App\Models\Recibo;
use App\Models\SaldoFavor;
use App\Models\Socio;
use App\Models\TipoPago;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Container extends Component
{
    #[Locked]
    public Socio $socio;

    public $cargosSeleccionados = [];   //Los cargos que se seleccionan en el modal
    public $totalSeleccionado;          //La suma total de saldo, de los cargos seleccionados

    public $cargosTabla = [];           //Los cargos que se muestran en la tabla
    public $metodoPagoGeneral;          //El metodo de pago que se selecciona para aplicar a todos los cargos
    public $observaciones;              //Observaciones generales

    #[Locked]
    public $totalAbono = 0, $totalSaldo = 0, $saldoFavor = 0;

    #[Computed()]
    public function listaCargos()
    {
        //Buscamos todos los cargos no pagados del estado de cuenta, del socio correspondiente
        return EstadoCuenta::where('id_socio', $this->socio->id)
            ->where('saldo', '>', 0)
            ->get();
    }

    #[Computed()]
    public function listaPagos()
    {
        //Buscamos todos los metodos de pago disponibles para un cobro
        return TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'FIRMA');
        })->get();
    }

    #[Computed()]
    public function caja() //Devuelve un array, con los registros de la BD
    {
        //Buscamos si hay una caja abierta en recepcion, con el usuario actual
        return DB::table('cajas')
            ->join('puntos_venta', 'cajas.clave_punto_venta', '=', 'puntos_venta.clave')
            ->select('cajas.*', 'puntos_venta.nombre')
            ->where('cajas.fecha_cierre', null)
            ->where('cajas.id_usuario', auth()->user()->id)
            ->where('puntos_venta.nombre', 'like', '%RECEP%')
            ->limit(1)
            ->get();
    }

    #[Computed()]
    public function saldoFavorDisponible()
    {
        //Buscamos el saldo favor disponible del socio
        return DB::table('recibos')
            ->join('saldo_favor', 'recibos.folio', '=', 'saldo_favor.folio_recibo_origen')
            ->select('recibos.id_socio', 'saldo_favor.*')
            ->where('id_socio', $this->socio->id)
            ->whereNull('aplicado_a')
            ->get();
    }

    //Actualizamos el total que se muestra en el modal
    public function updatedCargosSeleccionados()
    {
        //Filtramos los cargos seleccionados, cuyo valor sea true
        $cargosFiltrados = array_filter($this->cargosSeleccionados, function ($val) {
            return $val;
        });
        //Creamos array auxiliar
        $resultCargos = [];
        //Recorremos todos los cargos filtrados y los agregamos al array.
        foreach ($cargosFiltrados as $key => $value) {
            array_push($resultCargos, $this->listaCargos->find($key)->toArray());
        }
        $this->totalSeleccionado = array_sum(array_column($resultCargos, 'cargo'));
    }

    public function mount()
    {
        $this->socio = new Socio();
    }

    private function messages()
    {
        return [
            'cargosTabla.min' => 'Debes seleccionar al menos 1 cargo',
            'socio.required' => 'El socio es requerido',
        ];
    }

    #[On('on-selected-socio')]
    public function onSelectSocio($data)
    {
        /*Cuando se selecciona un nuevo socio del autocomplete,
            limpiar los datos del socio anterior y buscar al nuevo socio.
        */
        $this->reset();
        $this->socio = Socio::find($data);
    }

    public function finishSelect()
    {
        //Filtramos los cargos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->cargosSeleccionados, function ($val) {
            return $val;
        });
        //Si ha seleccionado al menos 1 articulo
        if (count($total_seleccionados) > 0) {
            DB::transaction(function () use ($total_seleccionados) {
                foreach ($total_seleccionados as $key => $value) {

                    //Buscamos el cargo en la base de datos
                    $cargo = EstadoCuenta::find($key);

                    //Buscamos si el cargo seleccionado, se encuentra en el array cargosTabla
                    $result = array_filter($this->cargosTabla, function ($cargo) use ($key) {
                        return $cargo['id'] == $key;
                    });

                    //Si el cargo existe al almenos una vez
                    if (count($result) > 0) {
                        //comprobar la sumatoria de sus montos de pago
                        $sumaMontos = array_sum(array_column($result, 'monto_pago'));
                        //Si la suma es menor que el saldo anterior
                        if ($sumaMontos < $cargo->saldo) {
                            //Agregamos el cargo a la lista, con monto de pago 0
                            array_push($this->cargosTabla, [
                                'id' => $key,
                                'concepto' => $cargo->concepto,
                                'id_tipo_pago' => null,
                                'saldo_anterior' => $cargo->saldo,      //Este saldo anterior, es informativo
                                'monto_pago' => 0
                            ]);
                        }
                    } else {
                        //Agregamos el cargo a la lista, que se vera en la tabla
                        array_push($this->cargosTabla, [
                            'id' => $key,
                            'concepto' => $cargo->concepto,
                            'id_tipo_pago' => null,
                            'saldo_anterior' => $cargo->saldo,      //Este saldo anterior, es informativo
                            'monto_pago' => $cargo->saldo
                        ]);
                    }
                }
            });
            //Emitimos evento para cerrar el modal
            $this->dispatch('close-modal');

            //Calculamos el total de los cargos seleccionados
            $this->calcularTotales();
            $this->reset('cargosSeleccionados');
        }
    }

    public function validarCargo($cargoIndex)
    {
        $this->validate([
            'cargosTabla.' . $cargoIndex . '.id_tipo_pago' => 'required',
            'cargosTabla.' . $cargoIndex . '.monto_pago'  => 'required|numeric|min:1'
        ], [
            'cargosTabla.*.id_tipo_pago.required' => 'El tipo de pago es obligatorio',
            'cargosTabla.*.monto_pago.required' => 'Obligatorio',
            'cargosTabla.*.monto_pago.numeric' => 'Número',
            'cargosTabla.*.monto_pago.min' => 'Mínimo: 1',
        ]);
    }

    public function aplicarCobro()
    {
        //Validamos datos previos
        $this->validate([
            'socio' => 'required',
            'cargosTabla' => 'min:1'
        ], $this->messages());
        foreach ($this->cargosTabla as $cargoIndex => $cargo) {
            //Validamos el tipo de pago de cada uno de los cargos
            $this->validarCargo($cargoIndex);
        }

        $metodoPago = TipoPago::where('descripcion', 'like', '%SALDO%')->get()[0];  //buscar el metodo de pago de saldo a favor

        //Filtramos los cargos en la tabla, con metodo de pago, saldo a favor
        $cargosSaldoFavor = array_filter($this->cargosTabla, function ($cargo) use ($metodoPago) {
            return ($cargo['id_tipo_pago'] == $metodoPago->id);
        });

        //Si existe al menos 1 cargo en la tabla con saldo a favor
        if (count($cargosSaldoFavor) > 0) {
            //Si no tiene saldo a favor disponible
            if (!count($this->saldoFavorDisponible) > 0) {
                session()->flash('fail', "No cuenta con saldo a favor");
                $this->dispatch('action-message-pago');
                return;
            } else {
                $sumaSaldoAFavor = 0;       //Variable acumuladora del saldo a favor utilizado
                //Sumar todos los montos de pago, de los conceptos con metodo de pago 'saldo a favor'
                array_map(function ($cargo) use (&$sumaSaldoAFavor, $metodoPago) {
                    if ($cargo['id_tipo_pago'] == $metodoPago->id) {
                        $sumaSaldoAFavor += $cargo['monto_pago'];
                    }
                }, $this->cargosTabla);

                //Comprobar si la suma de los montos con metodo de pago 'saldo a favor' es diferente al saldo a favor disponible
                if ($sumaSaldoAFavor != array_sum(array_column($this->saldoFavorDisponible->toArray(), 'saldo'))) {
                    //MENSAJE DE SESION
                    session()->flash('fail', "El saldo a favor debe ser aplicado en su totalidad");
                    //EVENTO PARA ABRIR EL ALERT
                    $this->dispatch('action-message-pago');
                    return;
                }
            }
        }

        //Si no hay caja abierta
        if (!count($this->caja) > 0) {
            //MENSAJE DE SESION
            session()->flash('fail', "No hay caja abierta");
            //EVENTO PARA ABRIR EL ALERT
            $this->dispatch('action-message-pago');
            return;
        }

        DB::transaction(function () use ($cargosSaldoFavor) {
            //Creamos el registro del recibo
            $result = Recibo::create([
                'id_socio' => $this->socio->id,
                'nombre' => $this->socio->nombre . ' ' . $this->socio->apellido_p . ' ' . $this->socio->apellido_m,
                'total' => $this->totalAbono,
                'corte_caja' => $this->caja[0]->corte,
                'observaciones' => $this->observaciones
            ]);
            //Si se utilizo saldo a favor en la tabla, se actualizan los registros utilizados 
            if (count($cargosSaldoFavor) > 0) {
                foreach ($this->saldoFavorDisponible as $registro) {
                    $resultSaldo = SaldoFavor::find($registro->id);
                    //Se actualiza el campo 'aplicado_a' del saldo a favor disponible
                    $resultSaldo->update([
                        'aplicado_a' => $result->folio
                    ]);
                }
            }
            //Si genero saldo a favor, se guarda.
            if ($this->saldoFavor > 0) {
                SaldoFavor::create([
                    'folio_recibo_origen' => $result->folio,
                    'saldo' => $this->saldoFavor,
                ]);
            }
            //Creamos los registros de los cargos
            foreach ($this->cargosTabla as $cargoIndex => $cargo) {
                //Buscamos el cargo del estado de cuenta
                $resultCargo = EstadoCuenta::find($cargo['id']);
                //Creamos el detalle del recibo, con el saldo anterior y el nuevo saldo.
                DB::table('detalles_recibo')
                    ->insert([
                        'folio_recibo' => $result->folio,
                        'id_estado_cuenta' => $cargo['id'],
                        'id_tipo_pago' => $cargo['id_tipo_pago'],
                        'saldo_anterior' => $resultCargo->saldo,
                        'monto_pago' => $cargo['monto_pago'],
                        'saldo' => ($cargo['monto_pago'] <= $resultCargo->saldo) ? $resultCargo->saldo - $cargo['monto_pago'] :  0, //Si el monto es mayor al saldo, el saldo es 0
                        'saldo_favor_generado' => ($cargo['monto_pago'] > $resultCargo->saldo) ?  $cargo['monto_pago'] - $resultCargo->saldo :  0, //Almacenamos el saldo a favor generado, en los detalles del recibo
                    ]);
                //Actualizamos el estado de cuenta
                $resultCargo->saldo_favor += ($cargo['monto_pago'] > $resultCargo->saldo) ?  $cargo['monto_pago'] - $resultCargo->saldo :  0;
                $resultCargo->abono += $cargo['monto_pago'];
                $resultCargo->saldo = ($cargo['monto_pago'] <= $resultCargo->saldo) ? $resultCargo->saldo - $cargo['monto_pago'] : 0;
                //dd($resultCargo);
                $resultCargo->save();
            }
            //Emitimos evento para abrir nueva pestaña
            $this->dispatch('ver-recibo', ['folio' => $result->folio]);
        });
        //LIMPIAR LOS ATRIBUTOS
        $this->reset();
        $this->socio = new Socio();

        //MENSAJE DE SESION
        session()->flash('success', "Cobro generado con exito");
        //EVENTO PARA ABRIR EL ALERT
        $this->dispatch('action-message-pago');
    }

    public function removerCargo($index)
    {
        //Eliminamos el cargo de la tabla
        unset($this->cargosTabla[$index]);
        //Calculamos el total de los cargos restantes   
        $this->calcularTotales();
    }

    //Se ejecuta cada vez que el usuario aplica un metodo de pago general
    public function aplicarMetodosPago($eValue)
    {
        //Si el valor del evento no es nullo
        if ($eValue != "") {
            //Actualizamos el 'id_tipo_pago' de cada cargo, por el general
            $this->cargosTabla = array_map(function ($cargo) {
                $cargo['id_tipo_pago'] = $this->metodoPagoGeneral;
                return $cargo;
            }, $this->cargosTabla);
        }
    }

    public function calcularTotales()
    {
        $cargosAux = [];        //array de cargos auxiliar que almacena los cargos sin repetir
        //recorremos todo el array de la tabla para filtrar aquellos repetidos
        foreach ($this->cargosTabla as $key => $cargoPuntero) {
            //Buscamos si el cargo actual del ciclo, se encuentra en el array auxiliar
            $result = array_filter($cargosAux, function ($cargo) use ($cargoPuntero) {
                return $cargo['id'] == $cargoPuntero['id'];
            });
            //Si el cargo no existe en la tabla, se agrega al array auxiliar
            if (count($result) == 0) {
                $cargosAux[] = $cargoPuntero;
            }
        }

        //Calculamos el total del monto abonado
        $this->totalAbono = array_sum(array_column($this->cargosTabla, 'monto_pago'));

        $this->totalSaldo = array_sum(array_column($cargosAux, 'saldo_anterior'));

        //Calculamos el saldo a favor que se genera en este recibo
        if ($this->totalAbono > $this->totalSaldo) {
            $this->saldoFavor = $this->totalAbono - $this->totalSaldo;
        } else {
            $this->saldoFavor = 0;
        }
    }
    public function render()
    {
        return view('livewire.recepcion.cobros.nuevo.container');
    }
}

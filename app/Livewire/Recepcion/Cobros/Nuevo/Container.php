<?php

namespace App\Livewire\Recepcion\Cobros\Nuevo;

use App\Models\Caja;
use App\Models\EstadoCuenta;
use App\Models\Recibo;
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

    public $cargosTabla = [];           //Los cargos que se muestran en la tabla
    public $metodoPagoGeneral;          //El metodo de pago que se selecciona para aplicar a todos los cargos
    public $observaciones;              //Observaciones generales

    #[Locked]
    public $totalAbono = 0, $totalSaldo = 0;

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
        $this->reset('cargosSeleccionados', 'cargosTabla');
        $this->socio = Socio::find($data);
    }

    public function finishSelect()
    {
        //Filtramos los productos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->cargosSeleccionados, function ($val) {
            return $val;
        });
        //Si ha seleccionado al menos 1 articulo
        if (count($total_seleccionados) > 0) {
            DB::transaction(function () use ($total_seleccionados) {
                foreach ($total_seleccionados as $key => $value) {
                    //Buscamos el producto en la base de datos
                    $cargo = EstadoCuenta::find($key);
                    //Agregamos el cargo a la lista, que se vera en la tabla
                    array_push($this->cargosTabla, [
                        'id_estado_cuenta' => $key,
                        'concepto' => $cargo->concepto,
                        'id_tipo_pago' => null,
                        'saldo_anterior' => $cargo->saldo,      //Este saldo anterior, es informativo
                        'monto_pago' => $cargo->saldo
                    ]);
                }
            });
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

        //Si no hay caja abierta
        if (!count($this->caja) > 0) {
            //MENSAJE DE SESION
            session()->flash('fail', "No hay caja abierta");
            //EVENTO PARA ABRIR EL ALERT
            $this->dispatch('action-message-pago');
        } else {
            DB::transaction(function () {
                //Creamos el registro del recibo
                $result = Recibo::create([
                    'id_socio' => $this->socio->id,
                    'nombre' => $this->socio->nombre,
                    //'id_tipo_pago' => $this->metodoPagoGeneral,
                    'total' => $this->totalAbono,
                    'corte_caja' => $this->caja[0]->corte,
                    'fecha' => now()->format('Y-m-d H:i:s'),
                    'observaciones' => $this->observaciones
                ]);
                //Creamos los registros de los cargos
                foreach ($this->cargosTabla as $cargoIndex => $cargo) {
                    //Buscamos el cargo del estado de cuenta
                    $resultCargo = EstadoCuenta::find($cargo['id_estado_cuenta']);
                    //Creamos el detalle del recibo, con el saldo anterior y el nuevo saldo.
                    DB::table('detalles_recibo')
                        ->insert([
                            'folio_recibo' => $result->folio,
                            'id_estado_cuenta' => $cargo['id_estado_cuenta'],
                            'id_tipo_pago' => $cargo['id_tipo_pago'],
                            'saldo_anterior' => $resultCargo->saldo,
                            'monto_pago' => $cargo['monto_pago'],
                            'saldo' => $resultCargo->saldo - $cargo['monto_pago']
                        ]);
                    //Actualizamos el estado de cuenta
                    $resultCargo->saldo = $resultCargo->saldo - $cargo['monto_pago'];
                    $resultCargo->abono += $cargo['monto_pago'];
                    $resultCargo->save();
                }
            });
            //LIMPIAR LOS ATRIBUTOS
            $this->reset('cargosSeleccionados', 'cargosTabla', 'metodoPagoGeneral', 'observaciones', 'totalAbono', 'totalSaldo');
            $this->socio = new Socio();

            //MENSAJE DE SESION
            session()->flash('success', "Cobro generado con exito");
            //EVENTO PARA ABRIR EL ALERT
            $this->dispatch('action-message-pago');
        }
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
        $this->totalAbono = array_sum(array_column($this->cargosTabla, 'monto_pago'));
        $cargosAux = [];

        foreach ($this->cargosTabla as $key => $cargoPuntero) {
            //Buscamos si el cargo actual del ciclo, se encuentra en el array auxiliar
            $result = array_filter($cargosAux, function ($cargo) use ($cargoPuntero) {
                return $cargo['id_estado_cuenta'] == $cargoPuntero['id_estado_cuenta'];
            });
            //Si el cargo no existe en la tabla, se agrega al array auxiliar
            if (count($result) == 0) {
                $cargosAux[] = $cargoPuntero;
            }
        }
        $this->totalSaldo = array_sum(array_column($cargosAux, 'saldo_anterior'));
    }
    public function render()
    {
        return view('livewire.recepcion.cobros.nuevo.container');
    }
}

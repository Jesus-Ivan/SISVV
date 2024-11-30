<?php

namespace App\Livewire\Sistemas\Recepcion\Anualidad;

use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Nueva extends Component
{
    #[Locked]
    public $socio;            //Informacion del socio

    #[Locked]
    public $socio_membresia;  //Informacion de la membresia el socio

    public $fInicio;          //Fecha de inicio de la anualidad
    #[Locked]
    public $fFin;             //Fecha de fin de la anualidad
    public $fecha_cont = 0;   //contador auxiliar para la fecha

    public $search;

    public $listaCuotas = [];

    public $total = 0;
    //Almacena el estado de la membresia que se seteara en la BD. al finalizar la anualidad
    public $estado_finalizar = 'MEN';
    public $membresia_finalizar;

    public $incremento, $descuento, $iva, $membresia_anterior, $membresia_nueva;
    public $saldo_cero = false;     //checkbox que indica si la anualidad debe registrase liquidada.
    public $no_corrida = 12;
    public $descuento_extra, $observaciones;

    #[On('on-selected-socio')]
    public function selectedSocio(Socio $socio)
    {
        //Cada vez que se selecciona un socio, lo guardamos como propiedad, en un array
        $this->socio = $socio->toArray();
        //Obtenemos la membresia actual del socio (junto la relacion con la descripcion de la membresia)
        $this->socio_membresia = SocioMembresia::with('membresia')
            ->where('id_socio', $socio->id)
            ->first();
    }

    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')
            ->where('tipo', 'like', '%ANU%')
            ->limit(15)
            ->get();
    }

    #[Computed()]
    public function membresias()
    {
        return Membresias::all();
    }

    /**
     * Hook que se ejecuta cuando se actualiza una propiedad
     */
    public function updated($properity, $value)
    {
        //Si la propiedad actualizada es la fecha de inicio
        if ($properity == 'fInicio') {
            try {
                $fecha_fin = Carbon::parse($value)->addMonths($this->no_corrida - 1); //Obtenemos la fecha de fin de la anualidad
                $this->fFin = $fecha_fin->toDateString(); //Establecemos la fecha de fin de la anualidad.
            } catch (\Throwable $th) {
                $this->fFin = now()->toDateString();
            }
        }

        //Si la propiedad actualizada es el numero de corrida
        if ($properity == 'no_corrida') {
            try {
                $fecha_fin = Carbon::parse($this->fInicio)->addMonths($value - 1); //Obtenemos la fecha de fin de la anualidad
                $this->fFin = $fecha_fin->toDateString(); //Establecemos la fecha de fin de la anualidad.
            } catch (\Throwable $th) {
                $this->fFin = now()->toDateString();
            }
        }

        //Si la propiedad actualizada es el monto de una cuota
        if (preg_match("/listaCuotas\.\d+\.monto/i", $properity)) {
            //Actualizar el total de la anualidad
            $this->total = array_sum(array_column($this->listaCuotas, 'monto'));
        }
    }

    //Eliminar el cargo del array de cargos
    public function removeCuota($cargoIndex)
    {
        //Remover el cargo de la lista de cuotas
        unset($this->listaCuotas[$cargoIndex]);
        //Actualizar el total de la anualidad
        $this->total = array_sum(array_column($this->listaCuotas, 'monto'));
    }

    //Agrega el cargo de forma temporal al array de cargos
    public function addCuota($cuota)
    {
        //Evaluamos si la cuota seleccionada tiene una 'clave_membresia'
        if ($cuota['clave_membresia']) {
            //filtrar del array de cuotas, las cuotas que pertenescan a una membresia
            $result = array_filter($this->listaCuotas, function ($cargo) {
                return ($cargo['clave_membresia']);
            });
            //Si habia una cuota de membresia (anualidad)
            if (count($result) > 0) {
                session()->flash('fail', 'No puedes agregar dos cuotas de membresia');
                return;
            }
        }

        //Evaluamos si no hay fecha de inicio
        if (!$this->fInicio) {
            session()->flash('fail', 'No has seleccionado la fecha de incio de la anualidad');
            return;
        }

        //Evaluamos si no hay numero de corrida
        if (!$this->no_corrida) {
            session()->flash('fail', 'No has ingresado el numero de meses');
            return;
        }

        $cuota = $this->addYear($cuota, "/loc/i");
        $cuota = $this->addYear($cuota, "/res/i");

        if (!preg_match("/\d{4}$/i", $cuota['descripcion'])) {
            //Se agrega el numero del año de la anualidad, a la descripcion de la cuota
            $cuota['descripcion'] = $cuota['descripcion'] . ' ' . strval(Carbon::parse($this->fInicio)->year);
        }

        //Agregramos a la lista de cargos
        $this->listaCuotas[] = $cuota;
    }

    public function aplicarAnualidad()
    {
        $validatedInfo = $this->verificar();     //Revisamos si los campos de inicio no estan vacios

        try {
            DB::transaction(function () use ($validatedInfo) {
                //Insertamos la informacion de la anualidad
                $anualidad = Anualidad::create([
                    'id_socio' => $validatedInfo['socio']['id'],
                    'membresia_anterior' => $this->membresia_anterior,
                    'incremento_anual' => $this->incremento,
                    'membresia_nueva' => $this->membresia_nueva,
                    'descuento_membresia' => $this->descuento,
                    'descuento_extra' => $this->descuento_extra,
                    'iva' => $this->iva,
                    'observaciones' => $this->observaciones,
                    'clave_mem_f' => $validatedInfo['membresia_finalizar'],
                    'estado_mem_f' => $validatedInfo['estado_finalizar'],
                    'fecha_inicio' => $validatedInfo['fInicio'],
                    'fecha_fin' => $validatedInfo['fFin'],
                    'total' => $this->total,
                ]);
                //Creamos los registros en las otras tablas
                foreach ($validatedInfo['listaCuotas'] as $key => $cargo) {
                    //Insertamos registro en los detalles de anualidades
                    DB::table('detalles_anualidades')->insert([
                        'id_anualidad' => $anualidad->id,
                        'id_cuota' => $cargo['id'],
                        'descripcion' => $cargo['descripcion'],
                        'monto' => $cargo['monto'],
                    ]);
                    //Creamos los cargos en el estado de cuenta.
                    EstadoCuenta::create([
                        'id_cuota' => $cargo['id'],
                        'id_socio' => $this->socio['id'],
                        'concepto' => $cargo['descripcion'],
                        'fecha' => $validatedInfo['fInicio'],
                        'cargo' => $cargo['monto'],
                        'abono' => $this->saldo_cero ? $cargo['monto'] : 0,
                        'saldo' => $this->saldo_cero ? 0 : $cargo['monto'],
                    ]);
                }
                //Mensage de sesion para el alert-message
                session()->flash('success', 'Anualidad cargada correctamente');
                //Emitimos evento para abrir el action message
                $this->dispatch('action-message-venta');
                //Limpiamos componente
                $this->reset();
            }, 2);
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('action-message-venta');
        }
    }

    public function verificar()
    {
        $validated = $this->validate([
            'socio' => 'required',
            'fInicio' => 'required',
            'fFin' => 'required',
            'listaCuotas' => 'required|min:1',
            'estado_finalizar' => 'required',
            'membresia_finalizar' => 'required',
        ]);
        return $validated;
    }

    /**
     * Dada una cuota y una expresion regular. 
     * Agrega el numero de cuotas en la tabla y el numero de año al final del concepto.
     */
    private function addYear($cuota, $reg_exp)
    {
        //Si la cuota coincide con el tipo
        if (preg_match($reg_exp, $cuota['tipo'])) {
            //Buscamos si ya habia cuotas en la tabla
            $cuotas = array_filter($this->listaCuotas, function ($cuotaItem) use ($reg_exp) {
                return preg_match($reg_exp, $cuotaItem['tipo']);
            });
            //Se agrega el numero del año de la anualidad, a la descripcion de la cuota
            $cuota['descripcion'] = $cuota['descripcion'] . ' ' . strval(count($cuotas) + 1) . ' ' . strval(Carbon::parse($this->fInicio)->year);
        }
        return $cuota;
    }
    public function render()
    {
        return view('livewire.sistemas.recepcion.anualidad.nueva');
    }
}

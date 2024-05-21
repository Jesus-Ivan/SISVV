<?php

namespace App\Livewire\Recepcion\Estados;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

use function PHPUnit\Framework\throwException;

class CargosNuevo extends Component
{
    #[Locked]
    public $socio;
    #[Locked]
    public $socioMembresia;

    public $search;
    public $listaCargos = [];

    public $fechaDestino;

    public function mount($socio)
    {
        $this->socio = $socio;
        $this->socioMembresia = SocioMembresia::with('membresia')->where('id_socio', $socio->id)->get()[0];
    }

    //Propiedad computarizada que pobla los resultados de busqueda
    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')->limit(10)->get();
    }

    //Agrega el cargo de forma temporal al array de cargos
    public function addCuota($cuota)
    {
        //Validamos si existe una fecha seleccionada
        $this->validate([
            'fechaDestino' => 'required|date'
        ]);
        //Agregamos el campo fecha al cargo.
        $cuota['fecha'] = $this->fechaDestino;

        //Evaluamos si la cuota seleccionada tiene una 'clave_membresia' null
        if ($cuota['clave_membresia']) {
            //Creamos instancia de carbon, de la fecha de la cuota
            $fechaCuota = Carbon::parse($cuota['fecha']);
            //Verificamos si hay una cuota de membresia ya existente en la BD
            $resultCargos = DB::table('estados_cuenta')
                ->join('cuotas', 'estados_cuenta.id_cuota', '=', 'cuotas.id')
                ->whereYear('estados_cuenta.fecha', '=', $fechaCuota->year)
                ->whereMonth('estados_cuenta.fecha', '=', $fechaCuota->month)
                ->whereNotNull('cuotas.clave_membresia')
                ->get();
            //Si se encuentra un cargo de alguna membresia, mensaje de error
            if (count($resultCargos) > 0) {
                session()->flash('fail', 'Ya existe un cargo registrado previamente en este mes');
                return;
            }

            //Verificamos si la cuota corresponde a la membresia
            if (!($cuota['tipo'] == $this->socioMembresia->estado && $cuota['clave_membresia'] == $this->socioMembresia->clave_membresia)) {
                session()->flash('fail', 'La cuota no corresponde a la membresia');
                return;
            } else {
                //Si la cuota es la correcta, verificar que no se agregue dos veces al array de 'listaCargos'
                $result = array_filter($this->listaCargos, function ($cargo) use ($cuota) {
                    $fechaCuota = Carbon::parse($cuota['fecha']);
                    $fechaVal = Carbon::parse($cargo['fecha']);
                    return ($cargo['id'] == $cuota['id'] && $fechaCuota->year == $fechaVal->year && $fechaCuota->month == $fechaVal->month);
                });
                if (count($result) > 0) {
                    session()->flash('fail', 'No puedes agregar la cuota dos veces en el mes');
                    return;
                }
            }
        }
        //Transformamos la fecha, en instancia de carbon
        $mensualidad = Carbon::parse($cuota['fecha']);
        //Concatenamos el nombre del mes y año a la descripcion de la cuota. 
        $cuota['descripcion'] = $cuota['descripcion'] . ' ' . $this->getMes($mensualidad->month) . '-' . $mensualidad->year;
        //Agregramos a la lista de cargos
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

        try {
            //Creamos una transaccion para que si hay un error, no se guarden los datos
            DB::transaction(function () {
                //Actualizamos el estado de cuenta
                foreach ($this->listaCargos as $key => $cargo) {
                    EstadoCuenta::create([
                        'id_cuota' => $cargo['id'],
                        'id_socio' => $this->socio->id,
                        'concepto' => $cargo['descripcion'],
                        'fecha' => $cargo['fecha'],
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
                return 'ENERO';
            case 2:
                return 'FEBRERO';
            case 3:
                return 'MARZO';
            case 4:
                return 'ABRIL';
            case 5:
                return 'MAYO';
            case 6:
                return 'JUNIO';
            case 7:
                return 'JULIO';
            case 8:
                return 'AGOSTO';
            case 9:
                return 'SEPTIEMBRE';
            case 10:
                return 'OCTUBRE';
            case 11:
                return 'NOVIEMBRE';
            case 12:
                return 'DICIEMBRE';
        }
    }
    public function render()
    {
        return view('livewire.recepcion.estados.cargos-nuevo');
    }
}

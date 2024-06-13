<?php

namespace App\Livewire\Recepcion\Estados;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\Socio;
use App\Models\SocioCuota;
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
    public $listaCargosFijos = [];
    public $listaCargosEliminados = []; //Esta lista almacena los id, de los cargos fijos originales, que fueron eliminados

    public $fechaDestino;

    public function mount($socio)
    {
        $this->socio = $socio;
        $this->socioMembresia = SocioMembresia::with('membresia')->where('id_socio', $socio->id)->get()[0];
        //Buscamos los cargos fijos del socio
        $this->listaCargosFijos = $this->obtenerCargosFijos($socio);
    }

    //Propiedad computarizada que pobla los resultados de busqueda
    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')->limit(10)->get();
    }

    //Propiedad computarizada que pobla los resultados de busqueda de los cargos fijos
    #[Computed()]
    public function cuotasFijas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')
            ->where('tipo', 'CAR')
            ->limit(10)
            ->get();
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
                ->where('estados_cuenta.id_socio', $this->socio->id)
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

    //Agregar cuota fija
    public function addCuotaFija($cuota)
    {
        $this->listaCargosFijos[] = [
            'id_socio' => $this->socio->id,
            'id_cuota' => $cuota['id'],
            'cuota' => [
                'id' => $cuota['id'],
                'descripcion' => $cuota['descripcion'],
                'monto' => $cuota['monto'],
                'tipo' => $cuota['tipo'],
                'clave_membresia' => $cuota['clave_membresia'],
            ],
        ];
    }

    public function verDatos()
    {
        dump($this->listaCargosFijos);
    }

    //Eliminar el cargo del array de cargos
    public function removeCuota($cargoIndex)
    {
        unset($this->listaCargos[$cargoIndex]);
    }

    public function removerCargoFijo($cargoIndex)
    {
        //Comprobamos si el id del cargo fijo, estaba definido en el array
        if (isset($this->listaCargosFijos[$cargoIndex]['id'])) {
            //guardamos el id, de la cuota fija del sicio, para eliminar despues
            array_push($this->listaCargosEliminados, $this->listaCargosFijos[$cargoIndex]['id']);
        }
        unset($this->listaCargosFijos[$cargoIndex]);
    }

    public function guardarCambios()
    {
        try {
            //Creamos una transaccion para que si hay un error, no se guarden los datos
            DB::transaction(function () {
                //Actualizamos el estado de cuenta con los cargos aplicados a un mes
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
                //Recorremos el array para crear los nuevos cargos fijos
                foreach ($this->listaCargosFijos as $key => $cargoFijo) {
                    if (!array_key_exists('id', $cargoFijo)) {
                        SocioCuota::create([
                            'id_socio' => $this->socio->id,
                            'id_cuota' => $cargoFijo['cuota']['id'],
                        ]);
                    }
                }
                foreach ($this->listaCargosEliminados as $id) {
                    SocioCuota::destroy($id);
                }
                //Volvemos a cargar la 'listaCargosFijos'
                $this->listaCargosFijos = $this->obtenerCargosFijos($this->socio);
            });
            //Emitimos evento para abrir alert
            $this->dispatch('action-message-cargos');
            //Enviamos mensaje de sesion
            session()->flash('success', 'Cargos registrados con éxito');
            //Limpiamos la listaDe cargos mensuales y de los eliminados-fijos
            $this->reset(['listaCargos', 'listaCargosEliminados']);
        } catch (Exception $e) {
            $this->dispatch('action-message-cargos');
            session()->flash('fail', $e->getMessage());
        }
    }

    private function obtenerCargosFijos($socio)
    {
        return SocioCuota::with('cuota')->where('id_socio', $socio->id)->get()->toArray();
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

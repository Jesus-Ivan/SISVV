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
    public $fecha_cont = 0;   //contador auxiliar para la fecha

    public $search;

    public $listaCuotas = [];
    public $listaResultados = [];

    public $total = 0;
    //Almacena el estado de la membresia que se seteara en la BD. al finalizar la anualidad
    public $estado_finalizar = 'MEN';
    public $membresia_finalizar;

    public $incremento, $descuento, $iva, $membresia_anterior, $membresia_nueva;
    public $saldo_cero = false;     //checkbox que indica si la anualidad debe registrase liquidada.

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

    //Eliminar el cargo del array de cargos
    public function removeCuota($cargoIndex)
    {
        unset($this->listaCuotas[$cargoIndex]);
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

        //Agregramos a la lista de cargos
        $this->listaCuotas[] = $cuota;
    }

    public function calcularSiguiente()
    {
        //Verificamos los campos de entrada
        $this->verificar();
        //Creamos una instancia de la fecha de inicio
        $fecha = Carbon::parse($this->fInicio)->addMonths($this->fecha_cont);
        //Agregamos de cuotas a la tabla de resultados
        foreach ($this->listaCuotas as $key => $cuota) {
            //Anexamos al array el nuevo mes
            $this->listaResultados[] = [
                'id_cuota' => $cuota['id'],
                'descripcion' => $cuota['descripcion'] . ' ' . $this->fecha_cont + 1,
                'monto' => $cuota['monto'],
                'fecha' => $fecha->toDateString(),
                'batch' => $this->fecha_cont
            ];
        }
        //Aumentamos 1 mes, la fecha para la siguente iteracion.
        $this->fecha_cont++;
        //Recalculamos el total
        $this->updateTotal();
    }

    public function calcularAnterior()
    {
        //restamos 1 mes
        $this->fecha_cont--;
        //Filtramos las cuotas, que coincidan con el 'batch' 
        $resultado_filtrado = array_filter($this->listaResultados, function ($row) {
            return $row['batch'] != $this->fecha_cont;
        });
        //Reasignamos los valores a la tabla
        $this->listaResultados = $resultado_filtrado;

        //Recalculamos el total
        $this->updateTotal();
    }

    public function updateTotal()
    {
        $this->total = array_sum(array_column($this->listaResultados, 'monto'));
    }

    public function aplicarAnualidad()
    {

        $validatedInfo = $this->verificar();     //Revisamos si los campos de inicio no estan vacios
        //Validamos la lista de resultados antes de agregarlos
        $validatedResultados = $this->validate([
            'listaResultados' => 'min:1',
        ]);

        try {
            DB::transaction(function () use ($validatedInfo, $validatedResultados) {
                //Insertamos la informacion de la anualidad
                $anualidad = Anualidad::create([
                    'id_socio' => $validatedInfo['socio']['id'],
                    'membresia_anterior' => $this->membresia_anterior,
                    'incremento_anual' => $this->incremento,
                    'membresia_nueva' => $this->membresia_nueva,
                    'descuento_membresia' => $this->descuento,
                    'iva' => $this->iva,
                    'clave_mem_f' => $validatedInfo['membresia_finalizar'],
                    'estado_mem_f' => $validatedInfo['estado_finalizar'],
                    'fecha_inicio' => $validatedResultados['listaResultados'][0]['fecha'],
                    'fecha_fin' => end($validatedResultados['listaResultados'])['fecha'],
                    'total' => $this->total,
                ]);
                //Creamos los registros en las otras tablas
                foreach ($validatedResultados['listaResultados'] as $key => $cargo) {
                    //Insertamos registro en los detalles de anualidades
                    DB::table('detalles_anualidades')->insert([
                        'id_anualidad' => $anualidad->id,
                        'id_cuota' => $cargo['id_cuota'],
                        'descripcion' => $cargo['descripcion'],
                        'monto' => $cargo['monto'],
                    ]);
                    //Creamos los cargos en el estado de cuenta.
                    EstadoCuenta::create([
                        'id_cuota' => $cargo['id_cuota'],
                        'id_socio' => $this->socio['id'],
                        'concepto' => $cargo['descripcion'],
                        'fecha' => $cargo['fecha'],
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
            'listaCuotas' => 'min:1',
            'estado_finalizar' => 'required',
            'membresia_finalizar' => 'required',
        ]);
        return $validated;
    }
    public function render()
    {
        return view('livewire.sistemas.recepcion.anualidad.nueva');
    }
}

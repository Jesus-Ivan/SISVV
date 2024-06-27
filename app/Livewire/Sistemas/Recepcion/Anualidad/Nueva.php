<?php

namespace App\Livewire\Sistemas\Recepcion\Anualidad;

use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
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

    #[Locked]
    public $cuota_base;

    public $fInicio;          //Fecha de inicio de la anualidad

    public $incrementoAnual;  //Incremento de la membresia anual
    public $descuento;        //Descuento final

    public $search;

    public $listaCuotas = [];
    public $listaResultados = [];

    #[On('on-selected-socio')]
    public function selectedSocio(Socio $socio)
    {
        //Cada vez que se selecciona un socio, lo guardamos como propiedad, en un array
        $this->socio = $socio->toArray();
        //Obtenemos la membresia actual del socio
        $this->socio_membresia = SocioMembresia::where('id_socio', $socio->id)->first();
        //Obtenemeos la cuota mensual de la membresia actual del socio
        $this->cuota_base = Cuota::where('clave_membresia', $this->socio_membresia->clave_membresia)
            ->where('tipo', 'MEN')
            ->first();
    }

    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')
            ->limit(15)
            ->get();
    }

    //Eliminar el cargo del array de cargos
    public function removeCuota($cargoIndex)
    {
        unset($this->listaCuotas[$cargoIndex]);
    }

    //Agrega el cargo de forma temporal al array de cargos
    public function addCuota($cuota)
    {
        //Evaluamos si la cuota seleccionada tiene una 'clave_membresia' null
        if ($cuota['clave_membresia']) {
            //Verificamos si la cuota corresponde a la membresia
            if (!($cuota['tipo'] == $this->cuota_base->tipo && $cuota['clave_membresia'] == $this->cuota_base->clave_membresia)) {
                session()->flash('fail', 'La cuota no corresponde a la membresia');
                return;
            } else {
                //Si la cuota es la correcta, verificar que no se agregue dos veces al array de 'listaCargos'
                $result = array_filter($this->listaCuotas, function ($cargo) use ($cuota) {
                    return ($cargo['id'] == $cuota['id']);
                });
                if (count($result) > 0) {
                    session()->flash('fail', 'No puedes agregar la cuota dos veces');
                    return;
                }
            }
        }


        //Agregramos a la lista de cargos
        $this->listaCuotas[] = $cuota;
    }

    public function calcular()
    {
        $this->verificar();
        /**
         * Calcular primero el costo de las mensualidades finales
         */

        //Obtenemos la cantidad que incrementara la cuota mensualmente
        $incremento = $this->cuota_base['monto'] * ($this->incrementoAnual / 100);
        //Creamos un clon para almacenar la cuota con el nuevo monto
        $cuota_incrementada = $this->cuota_base;
        //Sumamos el incremento, a la mensualidad
        $cuota_incrementada['monto'] = $cuota_incrementada['monto'] +  $incremento;

        //Obtener la cuota de anualidad
        $cuota_anualidad = Cuota::where('clave_membresia', $this->cuota_base->clave_membresia)
            ->where('tipo', 'ANU')
            ->first();

        //Obtenemos el monto de los doce meses, ya incrementados
        $subTotal = $cuota_incrementada['monto'] * 12;
        //Calculamos descuento sobre el monto total de las membresias.
        $descuento = $subTotal * ($this->descuento / 100);
        //Restamos el descuento al monto de los doce meses, y obtenemos el monto unitario. (por mes)
        $totalCuotaMes = ($subTotal - $descuento) / 12;
        //Creamos una instancia de la fecha de inicio
        $fecha = Carbon::parse($this->fInicio);

        /**
         * Crear todas la cuotas. Y las membresias con el aumento.
         */
        //Son dos meses, crear 12 veces las cuotas.
        for ($i = 1; $i <= 12; $i++) {
            //para cada item en la listaCuotas, agregarlo en la listaResultados
            foreach ($this->listaCuotas as $key => $cuota) {
                //Si el id de la cuota, es el mismo que la cuota incrementada, crear un concepto diferente
                if ($cuota['id'] == $cuota_incrementada['id']) {
                    $this->listaResultados[] = [
                        'id_cuota' => $cuota_anualidad->id,
                        'descripcion' => $cuota_anualidad->descripcion . ' ' . $i,
                        'monto' => $totalCuotaMes,
                        'fecha' => $fecha->toDateString()
                    ];
                } else {
                    $this->listaResultados[] = [
                        'id_cuota' => $cuota['id'],
                        'descripcion' => "ANUALIDAD " . $cuota['descripcion'],
                        'monto' => $cuota['monto'],
                        'fecha' => $fecha->toDateString()
                    ];
                }
            }
            //Aumentamos 1 mes, la fecha para la siguente iteracion.
            $fecha->addMonth();
        }
    }

    public function aplicarAnualidad()
    {
        $this->verificar();     //Revisamos si los campos de inicio no estan vacios
        $fFin = Carbon::parse($this->fInicio)->addMonths(12);   //Creamos la fecha de fin
        $total = array_sum(array_column($this->listaResultados, 'monto'));  //obtenemos el total de la anualidad

        try {
            DB::transaction(function () use ($fFin, $total) {
                //Insertamos la informacion de la anualidad
                $anualidad = Anualidad::create([
                    'id_socio' => $this->socio['id'],
                    'incremento_anual' => $this->incrementoAnual,
                    'descuento_membresia' => $this->descuento,
                    'clave_memb_org'=>$this->socio_membresia->clave_membresia,
                    'estado_mem_org'=>$this->socio_membresia->estado,
                    'fecha_inicio' => $this->fInicio,
                    'fecha_fin' => $fFin->toDateString(),
                    'total' => $total,
                ]);
                //Creamos los registros en las otras tablas
                foreach ($this->listaResultados as $key => $cargo) {
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
                        'abono' => 0,
                        'saldo' => $cargo['monto'],
                    ]);
                }

                $this->reset();
            }, 2);
        } catch (\Throwable $th) {
            dump($th->getMessage());
        }
    }

    public function verificar()
    {
        $validated = $this->validate([
            'socio' => 'required',
            'fInicio' => 'required',
            'incrementoAnual' => 'required',
            'descuento' => 'required',
        ]);
    }
    public function render()
    {
        return view('livewire.sistemas.recepcion.anualidad.nueva');
    }
}

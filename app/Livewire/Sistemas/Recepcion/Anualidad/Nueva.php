<?php

namespace App\Livewire\Sistemas\Recepcion\Anualidad;

use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioCuota;
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

    #[Locked]
    public $listaCargosFijos = [];              //Cargos fijos del socio (socios_cuotas)
    #[Locked]
    public $listaCargosFijosEliminados = [];    //Ids de cargos fijos (no-membresia) marcados para borrarse cuando inicie la anualidad
    #[Locked]
    public $listaMembresiasCancelar = [];       //Membresias marcadas para cancelarse (CAN) cuando inicie la anualidad: [['clave','concepto']]
    #[Locked]
    public $estadoCuentaBorrados = [];          //Copias de los movimientos de estado de cuenta borrados, para poder deshacer

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
        //Cargamos los cargos fijos del socio y limpiamos los marcados del socio anterior
        $this->cargarCargosFijos();
        //Limpiamos el historial de deshacer del socio anterior
        $this->estadoCuentaBorrados = [];
    }

    //Verdadero si el cargo fijo corresponde a una membresia (tiene clave_membresia)
    private function esCargoDeMembresia($fijo): bool
    {
        $clave = $fijo['cuota']['clave_membresia'] ?? null;
        return !empty($clave) && $clave !== 'N/A';
    }

    //Marca un cargo fijo para borrar su cuota cuando inicie la anualidad.
    //Permitido para cargos SIN membresia (locker/resguardo) y para la membresia que entra
    //en la anualidad (pasa a ANU, por lo que su cuota mensual debe quitarse). Las demas
    //membresias se gestionan con cancelarMembresia (CAN).
    public function removerCargoFijo($index)
    {
        if (!isset($this->listaCargosFijos[$index])) return;
        $fijo = $this->listaCargosFijos[$index];
        if ($this->esCargoDeMembresia($fijo)) {
            $clave = $fijo['cuota']['clave_membresia'];
            if (!$this->membresia_finalizar || $clave !== $this->membresia_finalizar) {
                session()->flash('fail', 'Esta membresia se quita con "Cancelar membresia". Sólo la membresia que entra en la anualidad se borra como cuota.');
                $this->dispatch('action-message-venta');
                return;
            }
        }
        $this->listaCargosFijosEliminados[] = $fijo['id'];
        unset($this->listaCargosFijos[$index]);
    }

    //Marca una membresia para cancelarse (CAN) cuando inicie la anualidad
    public function cancelarMembresia($index)
    {
        if (!isset($this->listaCargosFijos[$index])) return;
        $fijo = $this->listaCargosFijos[$index];

        if (!$this->esCargoDeMembresia($fijo)) {
            session()->flash('fail', 'Este cargo no es una membresia.');
            $this->dispatch('action-message-venta');
            return;
        }

        $clave = $fijo['cuota']['clave_membresia'];
        //No permitir cancelar la membresia que entra en la anualidad
        if ($this->membresia_finalizar && $clave === $this->membresia_finalizar) {
            session()->flash('fail', 'Esa membresia entra en la anualidad; no la canceles aqui.');
            $this->dispatch('action-message-venta');
            return;
        }

        $this->listaMembresiasCancelar[] = [
            'clave'    => $clave,
            'concepto' => $fijo['cuota']['descripcion'],
        ];
        unset($this->listaCargosFijos[$index]);
    }

    //Marca todos los cargos fijos visibles:
    // - membresia que entra en la anualidad -> se borra su cuota (pasa a ANU)
    // - otras membresias -> se cancelan (CAN)
    // - cargos sin membresia (locker/resguardo) -> se borran
    public function removerTodosCargosFijos()
    {
        foreach ($this->listaCargosFijos as $fijo) {
            if ($this->esCargoDeMembresia($fijo)) {
                $clave = $fijo['cuota']['clave_membresia'];
                if ($this->membresia_finalizar && $clave === $this->membresia_finalizar) {
                    $this->listaCargosFijosEliminados[] = $fijo['id'];   //membresia de la anualidad: borrar cuota
                } else {
                    $this->listaMembresiasCancelar[] = [
                        'clave'    => $clave,
                        'concepto' => $fijo['cuota']['descripcion'],
                    ];
                }
            } else {
                $this->listaCargosFijosEliminados[] = $fijo['id'];
            }
        }
        $this->listaCargosFijos = [];
    }

    //Deshace lo marcado, recargando los cargos fijos desde la BD
    public function restaurarCargosFijos()
    {
        $this->cargarCargosFijos();
    }

    //Borra de inmediato un movimiento del estado de cuenta del socio (borrado real),
    //guardando una copia para poder deshacer la accion.
    public function borrarEstadoCuenta($id)
    {
        if (!$this->socio) return;
        //Recuperamos el movimiento (acotado al socio) antes de borrarlo
        $mov = EstadoCuenta::where('id', $id)
            ->where('id_socio', $this->socio['id'])
            ->first();
        if (!$mov) return;
        //Guardamos una copia completa para poder restaurarlo (deshacer)
        $this->estadoCuentaBorrados[] = $mov->getAttributes();
        $mov->delete();
        //Limpiamos el cache de la propiedad computada para refrescar la tabla
        unset($this->estadoCuentaSocio);
    }

    //Deshace los borrados: vuelve a insertar los movimientos eliminados en esta sesion
    public function restaurarEstadoCuenta()
    {
        if (count($this->estadoCuentaBorrados) > 0) {
            //Reinsertamos las copias guardadas conservando sus datos originales (incluido el id)
            EstadoCuenta::insert($this->estadoCuentaBorrados);
            $this->estadoCuentaBorrados = [];
            unset($this->estadoCuentaSocio);
        }
    }

    private function cargarCargosFijos()
    {
        $this->listaCargosFijos = SocioCuota::with('cuota')
            ->where('id_socio', $this->socio['id'])
            ->orderBy('id_cuota')
            ->get()
            ->toArray();
        $this->listaCargosFijosEliminados = [];
        $this->listaMembresiasCancelar = [];
    }

    //Deja solo los ids de cuotas validas para borrar: cargos sin membresia, o la cuota de la
    //membresia que entra en la anualidad ($claveFinalizar). Evita borrar cuotas de membresias
    //activas distintas (que SocioForm recrearia, dejando un borrado no durable).
    private function filtrarCuotasEliminar($claveFinalizar): array
    {
        $ids = array_values(array_unique($this->listaCargosFijosEliminados));
        if (count($ids) === 0) return [];

        return SocioCuota::with('cuota')
            ->where('id_socio', $this->socio['id'])
            ->whereIn('id', $ids)
            ->get()
            ->filter(function ($sc) use ($claveFinalizar) {
                $clave = $sc->cuota->clave_membresia;
                return empty($clave) || $clave === 'N/A' || $clave === $claveFinalizar;
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();
    }

    #[Computed()]
    public function membresiasActivas()
    {
        if (!$this->socio) return collect();
        return SocioMembresia::with('membresia')
            ->where('id_socio', $this->socio['id'])
            ->whereNot('estado', 'CAN')
            ->get();
    }

    //Movimientos del estado de cuenta del socio (mas recientes primero).
    //Solo cuotas: cargo mayor a 0 y ligadas a una cuota (id_cuota no null).
    #[Computed()]
    public function estadoCuentaSocio()
    {
        if (!$this->socio) return collect();
        return EstadoCuenta::where('id_socio', $this->socio['id'])
            ->where('cargo', '>', 0)
            ->whereNotNull('id_cuota')
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();
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

        //Cuotas a borrar: cargos sin membresia + la cuota de la membresia que entra en la anualidad.
        //(filtra cuotas de membresias que ya no son la de la anualidad, p.ej. si se cambio el select)
        $cuotasFijasEliminar = $this->filtrarCuotasEliminar($validatedInfo['membresia_finalizar']);
        //Membresias a cancelar (CAN): nunca la que entra en la anualidad.
        $membresiasCancelar = array_values(array_filter(
            array_unique(array_column($this->listaMembresiasCancelar, 'clave')),
            fn($c) => $c !== $validatedInfo['membresia_finalizar']
        ));

        try {
            DB::transaction(function () use ($validatedInfo, $cuotasFijasEliminar, $membresiasCancelar) {
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
                    //Los cargos fijos marcados se eliminan hasta que el proceso mensual active la anualidad
                    'cuotas_fijas_eliminar' => count($cuotasFijasEliminar) > 0 ? $cuotasFijasEliminar : null,
                    //Las membresias marcadas se cancelan (CAN) cuando el proceso mensual active la anualidad
                    'membresias_cancelar' => count($membresiasCancelar) > 0 ? $membresiasCancelar : null,
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

                //Activación inmediata: sólo si la anualidad ya está vigente HOY (inicio
                //retroactivo o del día en curso), se aplica sin esperar la carga masiva. Se
                //compara por FECHA EXACTA: una anualidad que empieza en el futuro NO se activa
                //todavía (la membresía no debe mostrarse como ANU antes de su día de inicio).
                $hoy = now()->startOfDay();
                $inicio = Carbon::parse($anualidad->fecha_inicio)->startOfDay();
                $fin = Carbon::parse($anualidad->fecha_fin)->startOfDay();
                if ($inicio->lessThanOrEqualTo($hoy) && $fin->greaterThanOrEqualTo($hoy)) {
                    $anualidad->activar();
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
        return view('livewire.sistemas.recepcion.anualidad.nueva',[
            'var' => null
        ]);
    }
}

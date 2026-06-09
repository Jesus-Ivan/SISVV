<?php

namespace App\Livewire\Recepcion\Estados;

use App\Constants\RecepcionConstants;
use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CargosNuevo extends Component
{
    #[Locked]
    public $socio;
    #[Locked]
    public $socioMembresia;

    public $search;
    public $search_fijos;
    public $listaCargos = [];
    public $listaCargosFijos = [];
    public $listaCargosEliminados = []; //Esta lista almacena los id, de los cargos fijos originales, que fueron eliminados
    public $cargos_anualidad = [];      //Esta lista almacena los cargos incluidos en la anualidad

    public $fechaDestino;

    public function mount($socio)
    {
        $this->socio = $socio;
        //Primer no-CAN (o CAN si todas lo son) — misma lógica que el accessor del modelo
        $this->socioMembresia = SocioMembresia::with('membresia')
            ->where('id_socio', $socio->id)
            ->orderByRaw("FIELD(estado, 'CAN') ASC")
            ->orderBy('id')
            ->first();
        //Buscamos los cargos fijos del socio
        $this->listaCargosFijos = $this->obtenerCargosFijos($socio);
        //Si alguna membresía está en anualidad, cargar los detalles
        $tieneAnu = SocioMembresia::where('id_socio', $socio->id)->where('estado', 'ANU')->exists();
        if ($tieneAnu) {
            /*
             *Buscar los cargos incluidos en la anualidad
             */

            //Crear la fecha de hoy
            $hoy = now()->day(1);
            //Buscamos la anualidad
            $anualidad = Anualidad::where([
                ['id_socio', '=', $socio->id],
                ['fecha_inicio', '<=', $hoy->toDateString()],
                ['fecha_fin', '>=', $hoy->toDateString()],
            ])
                ->first();
            //Buscar los detalles de la anualidad
            $this->cargos_anualidad = DB::table('detalles_anualidades')
                ->where('id_anualidad', $anualidad->id)
                ->get();
        }
    }

    // true si TODAS las membresías son CAN (deshabilita el formulario)
    #[Computed()]
    public function todasCanceladas(): bool
    {
        return SocioMembresia::where('id_socio', $this->socio->id)
            ->where('estado', '!=', 'CAN')
            ->doesntExist();
    }

    // true si ALGUNA membresía está en ANU
    #[Computed()]
    public function tieneAnualidad(): bool
    {
        return SocioMembresia::where('id_socio', $this->socio->id)
            ->where('estado', 'ANU')
            ->exists();
    }

    //Propiedad computarizada que pobla los resultados de busqueda
    #[Computed()]
    public function cuotas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search . '%')
            ->whereNot('tipo', 'LIKE', '%ANU%')
            ->whereNull('clave_membresia')
            ->limit(10)
            ->get();
    }

    //Propiedad computarizada que pobla los resultados de busqueda de los cargos fijos
    #[Computed()]
    public function cuotasFijas()
    {
        return Cuota::where('descripcion', 'like', '%' . $this->search_fijos . '%')
            ->where([
                ['tipo', 'LIKE', '%CAR%'],
                ['tipo', 'LIKE', '%MEN%'],
            ])
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

            //Verificamos que la cuota corresponde a alguna membresía activa del socio
            $membresiaValida = SocioMembresia::where('id_socio', $this->socio->id)
                ->where('estado', $cuota['tipo'])
                ->where('clave_membresia', $cuota['clave_membresia'])
                ->exists();
            if (!$membresiaValida) {
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
            'auto_delete' => false
        ];
    }

    // Carga un cargo fijo específico al array de cargos usando su monto_personalizado si existe
    public function cargarDesdeCargoFijo(int $indexFijo)
    {
        $this->validate(['fechaDestino' => 'required|date']);

        $fijo = $this->listaCargosFijos[$indexFijo];
        $cuotaId  = $fijo['cuota']['id'];
        $monto    = $fijo['monto_personalizado'] ?? $fijo['cuota']['monto'];
        $claveMem = $fijo['cuota']['clave_membresia'] ?? null;
        $fechaCuota = Carbon::parse($this->fechaDestino);

        // Para membresías: verificar que no exista ya un cargo en el mismo mes
        if ($claveMem && $claveMem !== 'N/A') {
            $yaExiste = DB::table('estados_cuenta')
                ->join('cuotas', 'estados_cuenta.id_cuota', '=', 'cuotas.id')
                ->where('estados_cuenta.id_socio', $this->socio->id)
                ->whereYear('estados_cuenta.fecha', $fechaCuota->year)
                ->whereMonth('estados_cuenta.fecha', $fechaCuota->month)
                ->where('cuotas.clave_membresia', $claveMem)
                ->exists();
            if ($yaExiste) {
                session()->flash('fail', 'Ya existe un cargo de esta membresía para el mes seleccionado');
                $this->dispatch('action-message-cargos');
                return;
            }
        } else {
            // Para cargos fijos sin membresía: comparar cuántos ya están en BD vs cuántos tiene el socio
            $cargadosEnBd = DB::table('estados_cuenta')
                ->where('id_socio', $this->socio->id)
                ->where('id_cuota', $cuotaId)
                ->whereYear('fecha', $fechaCuota->year)
                ->whereMonth('fecha', $fechaCuota->month)
                ->count();

            $pendientesEnLista = collect($this->listaCargos)->filter(function ($c) use ($cuotaId, $fechaCuota) {
                $f = Carbon::parse($c['fecha']);
                return $c['id'] == $cuotaId
                    && isset($c['socios_cuota_id'])
                    && $f->year == $fechaCuota->year
                    && $f->month == $fechaCuota->month;
            })->count();

            $totalInstancias = collect($this->listaCargosFijos)
                ->filter(fn($f) => $f['cuota']['id'] == $cuotaId)
                ->count();

            if ($cargadosEnBd + $pendientesEnLista >= $totalInstancias) {
                session()->flash('fail', 'Este cargo ya fue registrado en el estado de cuenta de este mes');
                $this->dispatch('action-message-cargos');
                return;
            }
        }

        // Evitar que se cargue la misma fila de socios_cuotas dos veces en el mismo mes
        $socioCuotaId = $fijo['id'];
        $duplicado = collect($this->listaCargos)->contains(function ($c) use ($socioCuotaId, $fechaCuota) {
            $f = Carbon::parse($c['fecha']);
            return ($c['socios_cuota_id'] ?? null) == $socioCuotaId
                && $f->year == $fechaCuota->year
                && $f->month == $fechaCuota->month;
        });
        if ($duplicado) {
            session()->flash('fail', 'Este cargo ya fue agregado para el mes seleccionado');
            $this->dispatch('action-message-cargos');
            return;
        }

        $baseDesc = $fijo['cuota']['descripcion'] . ' ' . $this->getMes($fechaCuota->month) . '-' . $fechaCuota->year;
        $textoConcepto = $fijo['texto_concepto'] ?? null;
        if ($textoConcepto) {
            $descripcion = ($fijo['posicion_texto'] ?? 'izquierda') === 'derecha'
                ? $baseDesc . ' ' . $textoConcepto
                : $textoConcepto . ' ' . $baseDesc;
        } else {
            $descripcion = $baseDesc;
        }

        $this->listaCargos[] = [
            'id'              => $cuotaId,
            'socios_cuota_id' => $socioCuotaId,
            'descripcion'     => $descripcion,
            'monto'           => $monto,
            'tipo'            => $fijo['cuota']['tipo'],
            'clave_membresia' => $claveMem,
            'fecha'           => $this->fechaDestino,
        ];
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
            //guardamos el id, de la cuota fija del socio, para eliminar despues
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
        return SocioCuota::with('cuota.membresia')
            ->where('id_socio', $socio->id)
            ->orderBy('id_cuota')
            ->get()
            ->toArray();
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
        return view(
            'livewire.recepcion.estados.cargos-nuevo',
            ['editable_cargo' => RecepcionConstants::EDITABLE_CARGO_KEY]
        );
    }
}

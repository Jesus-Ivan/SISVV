<?php

namespace App\Livewire\Lockers\Movimientos;

use App\Constants\LockersConstants;
use App\Models\Anualidad;
use App\Models\Cuota;
use App\Models\DetalleAnualidad;
use App\Models\IntegrantesSocio;
use App\Models\Locker;
use App\Models\Socio;
use App\Models\SocioCuota;
use App\Models\SocioMembresia;
use App\Services\LockerService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Alta extends Component
{
    //Valores de tipo de busqueda
    public array $tipo = [
        LockersConstants::KEY_SOCIO,
        LockersConstants::KEY_INTEGRANTE
    ];
    public string $tipo_selected = LockersConstants::KEY_SOCIO;

    //Seccion del locker
    public array $secciones = LockersConstants::ENUM_SECCION_LOCKER;
    //Estados del locker
    public array $estados = LockersConstants::ENUM_ESTADO_LOCKER;

    public string $seccion_general = '';    //Utilizado para definir la seccion de busqueda para locker excepcional
    public string $input_search = '';       //Define el numero de locker a buscar (excepcional)

    public $miembros = [];
    public $lockers_sistema = [];

    public array $lockers_excep = [];

    #[On('on-selected-socio')]
    public function onSelectedSocio(Socio $socio)
    {
        try {
            $this->setInitialState($socio);
        } catch (\Throwable $th) {
            //Emitimos mensaje de sesion (en caso de tener una membresia cancelada temporal)
            session()->flash('fail', $th->getMessage());
            $this->dispatch('action-message-locker');
        }
    }

    #[On('on-selected-integrante')]
    public function onSelectedIntegrante(IntegrantesSocio $integrante)
    {
        //Buscar socio original
        $socio = Socio::find($integrante->id_socio);
        try {
            if ($socio)
                $this->setInitialState($socio);
            else
                throw new Exception('Accion: ' . $integrante->id_socio . ' Cancelada.', 1);
        } catch (\Throwable $th) {
            //Emitimos mensaje de sesion (en caso de no encontrar al socio titular)
            session()->flash('fail', $th->getMessage());
            $this->dispatch('action-message-locker');
        }
    }

    public function guardarLockers(LockerService $lockerService)
    {
        //Obtener al usuario actual
        $usuario_sistema = auth()->user();
        //Validar que los locker tengan los parametros necesarios
        $this->validarLocker();
        $flag_error = false;    //Determina si hubo error en alguna iteracion. Al asignar lockers.

        foreach ($this->lockers_sistema as $key => $row) {
            //Si algun parametro no esta definido. omitir iteracion
            if ($row['seccion'] === '' || $row['numero'] === '' || $row['index_miembro'] === '') {
                continue;
            }

            try {
                $lockerService->asignarLocker(
                    $row['seccion'],
                    $row['numero'],
                    $this->miembros[$row['index_miembro']],
                    $usuario_sistema->name,
                    $row['observaciones'],
                    $row['id'],
                    array_key_exists('anualidad', $row)
                );

                unset($this->lockers_sistema[$key]); //Eliminar del array, una vez procesado

            } catch (\Throwable $th) {
                //Cambiar el flag en caso de encontrar error en alguna iteracion
                $flag_error = true;
                $new_message = $th->getMessage(); //Guardamos mensaje de sesion anterior 
                $old_message = session('fail', ''); //Obtenemos el nuevo mensaje de error
                //Concatenar el mensaje final
                session()->flash('fail', $old_message . "\n" . $new_message);
            }
        }

        if (!$flag_error) {
            session()->flash('success', 'Asignación de locker realizada.'); //Guardamos mensaje de sesion, en caso de ningun error.
            $this->reset();
            $this->limpiarLockers();    //Limpiar tabla
        }
        $this->dispatch('action-message-locker');
    }

    public function guardarLockersExcepcionales(LockerService $lockerService)
    {
        //Obtener al usuario actual
        $usuario_sistema = auth()->user();

        $this->validarLockerExcepcionales();

        try {
            foreach ($this->lockers_excep as $key => $row) {
                switch ($row['estado_actual']) {
                    case $this->estados[1]:
                        $lockerService->asignarLockerExcep(
                            $row['id_locker'],
                            $usuario_sistema->name,
                            $row['observaciones'],
                        );
                        break;
                    case $this->estados[2]:
                        $lockerService->mandarAMantenimiento(
                            $row['id_locker'],
                            $usuario_sistema->name,
                            $row['observaciones']
                        );
                        break;

                    default:
                        # code...
                        break;
                }
            }
            $this->reset();
            $this->limpiarLockers();    //Limpiar tabla

            //Emitimos mensaje de sesion 
            session()->flash('success', 'Asignación de locker realizada.');
        } catch (Exception $th) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', $th->getMessage());
        } finally {
            $this->dispatch('action-message-locker');
        }
    }

    public function eliminar($index)
    {
        unset($this->lockers_excep[$index]);
    }

    /**
     * Busca un locker disponible
     */
    public function buscar()
    {
        //Buscar el locker
        $locker = Locker::where([
            ['numero', '=', $this->input_search],
            ['seccion', '=', $this->seccion_general],
            ['estado_actual', '=', LockersConstants::ENUM_ESTADO_LOCKER[0]]
        ])->first();

        //Si no esta disponible
        if (!$locker) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', "El locker $this->input_search ($this->seccion_general) No esta disponible.");
            $this->dispatch('action-message-locker');
        } else {
            //Filtrar de la tabla de lockers, si ya se encuentra el mismo "id_locker"
            $locker_in_array = array_filter($this->lockers_excep, function ($row) use ($locker) {
                return $row['id_locker'] ==  $locker->id_locker;
            });
            //Si hay al menos 1 con el mismo 'id_locker'
            if (count($locker_in_array)) {
                //Emitimos mensaje de sesion 
                session()->flash('fail', "El locker $this->input_search ($this->seccion_general) Ya esta seleccionado.");
                $this->dispatch('action-message-locker');
            } else {
                //Agregar el locker a la tabla
                array_push($this->lockers_excep, $locker->toArray());
            }
        }
    }

    /**
     * Configura las propiedades iniciales del componente.
     */
    public function setInitialState(Socio $socio)
    {
        //Verificar si el socio tiene alguna membresia cancelada temporal
        $membresia_cancelada = SocioMembresia::where([
            ['id_socio', '=', $socio->id],
            ['estado', '=', 'CAN'],
        ])->first();
        if ($membresia_cancelada)
            throw new Exception('Accion: ' . $socio->id . ' Cancelada Temporal.', 1);

        $this->reset();
        //Limpiar tabla
        $this->limpiarLockers();

        //Buscar Integrantes
        $integrantes = IntegrantesSocio::where('id_socio', $socio->id)
            ->get();

        /**
         * Unificar lista
         */
        //Agregar socio titular
        array_push($this->miembros, [
            'id_socio' => $socio->id,
            'id_integrante' => null,
            'nombre' => $socio->nombre,
            'apellido_p' => $socio->apellido_p,
            'apellido_m' => $socio->apellido_m,
            'parentesco' => null,
            'img_path' =>  $socio->img_path
        ]);
        //Agregar integrantes
        foreach ($integrantes as $key => $integrante) {
            array_push($this->miembros, [
                'id_socio' => $socio->id,
                'id_integrante' => $integrante->id,
                'nombre' => $integrante->nombre_integrante,
                'apellido_p' => $integrante->apellido_p_integrante,
                'apellido_m' => $integrante->apellido_m_integrante,
                'parentesco' => $integrante->parentesco,
                'img_path' =>  $integrante->img_path_integrante
            ]);
        }

        //Obtener los id's de las cuotas que corresponden a lockers
        $locker_ids = Cuota::where('descripcion', 'like', '%LOCKER%')->get()->toarray();
        //Buscar las cuotas del socio (cuotas fijas)
        $cuotas = SocioCuota::whereIn('id_cuota', array_column($locker_ids, 'id'))
            ->whereNull('id_locker')
            ->where('id_socio', $socio->id)
            ->get()
            ->toArray();

        //Buscar las cuotas del socio (cuotas anuales activas)
        $hoy = now()->toDateString();
        $cuotas_anu = DetalleAnualidad::with('anualidad')
            ->whereHas('anualidad', function ($query) use ($hoy, $socio) {
                $query->where([
                    ['fecha_inicio', '<=', $hoy],
                    ['fecha_fin', '>=', $hoy]
                ])
                    ->where('id_socio', $socio->id);
            })
            ->whereIn('id_cuota', array_column($locker_ids, 'id'))
            ->whereNull('id_locker')
            ->get()
            ->toArray();
        //Unir ambos arrays en uno mismo
        $cuotas = [...$cuotas, ...$cuotas_anu];


        //Mapear propiedades extra
        $lockers_map = array_map(function ($row) {
            $row['index_miembro'] = '';
            $row['seccion'] = '';
            $row['numero'] = '';
            $row['observaciones'] = '';
            return $row;
        }, $cuotas);

        //Convertir indices a tipo string
        foreach ($lockers_map as $key_principal => $subArray) {
            $this->lockers_sistema['item_' . $key_principal] = $subArray;
        }
    }

    /**
     * Limpiar la tabla de los lockers seleccionados
     */
    public function limpiarLockers()
    {
        foreach ($this->lockers_sistema as $key => $row) {
            $row['index_miembro'] = '';
            $row['seccion'] = '';
            $row['numero'] = '';
            $row['observaciones'] = '';
        }
    }

    /**
     * Valida las propiedades de la tabla de lockers
     */
    public function validarLocker()
    {
        foreach ($this->lockers_sistema as $index => $locker_row) {
            //Si algun campo de entrada es diferente de vacio
            if ($locker_row['seccion'] !== '' || $locker_row['numero'] !== '' || $locker_row['index_miembro'] !== '') {
                //Validar las propiedades de toda la fila
                $this->validate([
                    'lockers_sistema.' . $index . '.seccion' => 'required',
                    'lockers_sistema.' . $index . '.numero' => 'required|numeric|min:1|max:300',
                    'lockers_sistema.' . $index . '.index_miembro'  => 'required',
                    'lockers_sistema.' . $index . '.observaciones'  => 'max:255'
                ], [
                    'lockers_sistema.*.seccion.required' => 'Obligatorio',
                    'lockers_sistema.*.numero.required' => 'Obligatorio',
                    'lockers_sistema.*.numero.numeric' => 'Numero',
                    'lockers_sistema.*.numero.min' => 'Min: 1',
                    'lockers_sistema.*.numero.max' => 'Max: 300',
                    'lockers_sistema.*.index_miembro.required' => 'Obligatorio',
                    'lockers_sistema.*.observaciones.max' => 'Max: 300',
                ]);
            }
        }
    }

    /**
     * Valida que los locker excepcionales tengan comentarios obligatorios
     */
    public function validarLockerExcepcionales()
    {
        $validated = $this->validate([
            'lockers_excep.*.estado_actual' => ['required', Rule::notIn($this->estados[0])],
            'lockers_excep.*.observaciones' => 'required',
        ], [
            'lockers_excep.*.estado_actual.required' => 'Obligatorio',
            'lockers_excep.*.estado_actual.not_in' => 'El estado no puede ser ' . $this->estados[0],
            'lockers_excep.*.observaciones.required' => 'Obligatorio',
        ]);

        return $validated;
    }

    public function render()
    {
        return view('livewire.lockers.movimientos.alta');
    }
}

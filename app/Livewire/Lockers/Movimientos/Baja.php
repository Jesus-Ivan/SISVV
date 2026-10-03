<?php

namespace App\Livewire\Lockers\Movimientos;

use App\Constants\LockersConstants;
use App\Models\Locker;
use App\Services\LockerService;
use Livewire\Component;

class Baja extends Component
{
    //Seccion del locker
    public array $secciones = LockersConstants::ENUM_SECCION_LOCKER;

    public string $seccion_general = '';    //Utilizado para definir la seccion de busqueda para locker excepcional
    public string $input_search = '';       //Define el numero de locker a buscar (excepcional)


    public array $lockers_excep = [];


    /**
     * Busca un locker disponible
     */
    public function buscar()
    {
        if (!$this->seccion_general || !$this->input_search) {
            return '';
        }
        //Buscar el locker
        $locker = Locker::where([
            ['numero', '=', $this->input_search],
            ['seccion', '=', $this->seccion_general],
        ])
            ->first();

        //validar el locker
        if ($locker?->estado_actual === LockersConstants::ENUM_ESTADO_LOCKER[0]) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', "El locker $this->input_search ($this->seccion_general) Ya esta disponible.");
            $this->dispatch('action-message-locker');
        } else {
            if ($locker->id_socio_actual || $locker->id_integrante_actual) {
                //Emitimos mensaje de sesion 
                session()->flash('fail', "El locker $this->input_search ($this->seccion_general) pertenece al socio: " . $locker->id_socio_actual);
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
                $this->reset('input_search');
            }
        }
    }

    public function eliminar($index)
    {
        unset($this->lockers_excep[$index]);
    }


    public function cancelarLockers(LockerService $lockerService)
    {
        //Obtener al usuario actual
        $usuario_sistema = auth()->user();
        try {
            foreach ($this->lockers_excep as $i => $locker) {
                switch ($locker['estado_actual']) {
                    case LockersConstants::ENUM_ESTADO_LOCKER[1]:
                        $lockerService->bajaLocker(
                            $locker['id_locker'],
                            $usuario_sistema->name,
                            $locker['observaciones'],
                        );
                        break;
                    case LockersConstants::ENUM_ESTADO_LOCKER[2]:
                        $lockerService->salirDeMantenimiento(
                            $locker['id_locker'],
                            $usuario_sistema->name,
                            $locker['observaciones']
                        );
                        break;
                    default:
                        # code...
                        break;
                }
            }
            $this->reset();
            //Emitimos mensaje de sesion 
            session()->flash('success', 'Baja(s) de locker realizada.');
        } catch (\Throwable $th) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', $th->getMessage());
        } finally {
            $this->dispatch('action-message-locker');
        }
    }


    public function render()
    {
        return view('livewire.lockers.movimientos.baja');
    }
}

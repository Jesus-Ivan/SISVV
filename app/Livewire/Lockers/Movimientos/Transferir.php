<?php

namespace App\Livewire\Lockers\Movimientos;

use App\Constants\LockersConstants;
use App\Models\Locker;
use App\Models\LockerSocioView;
use App\Services\LockerService;
use Livewire\Component;

class Transferir extends Component
{
    //Seccion del locker
    public array $secciones = LockersConstants::ENUM_SECCION_LOCKER;


    public string $seccion_general = '';    //Utilizado para definir la seccion de busqueda
    public string $input_search = '';       //Define el numero de locker a buscar

    public array $lockers = [];

    /**
     * Busca un locker ocupado para transferir de numero
     */
    public function buscar()
    {
        //Buscar el locker
        $locker = LockerSocioView::where([
            ['numero', '=', $this->input_search],
            ['seccion', '=', $this->seccion_general],
            ['estado_actual', '=', LockersConstants::ENUM_ESTADO_LOCKER[1]]
        ])->first();

        //Si no esta ocupado
        if (!$locker) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', "El locker $this->input_search ($this->seccion_general), Esta disponible o en Mantenimiento");
            $this->dispatch('action-message-locker');
        } else {
            //Filtrar de la tabla de lockers, si ya se encuentra el mismo "id_locker"
            $locker_in_array = array_filter($this->lockers, function ($row) use ($locker) {
                return $row['id_locker'] ==  $locker->id_locker;
            });

            //Si hay al menos 1 con el mismo 'id_locker'
            if (count($locker_in_array)) {
                //Emitimos mensaje de sesion 
                session()->flash('fail', "El locker $this->input_search ($this->seccion_general) Ya esta seleccionado.");
                $this->dispatch('action-message-locker');
            } else {
                $locker_f = $locker->toArray();
                $locker_f['destino'] = '';
                //Agregar el locker a la tabla
                array_push($this->lockers, $locker_f);
                $this->reset('input_search');   //Limpiar el campo de busqueda
            }
        }
    }


    public function guardarLockers(LockerService $lockerService)
    {
        //usuario del sistema
        $user = auth()->user();
        try {
            //iterar toda la tabla de lockers
            foreach ($this->lockers as $key => $loc) {
                //Buscar locker destino
                $loc_d = Locker::where([
                    ['seccion', '=', $loc['seccion']],
                    ['numero', '=', $loc['destino']]
                ])
                    ->first();
                $lockerService->transferirLocker($loc['id_locker'], $loc_d->id_locker, $user->name, $loc['observaciones']);
            }
            //Emitimos mensaje de sesion 
            session()->flash('success', '¡Transferencia de locker Exitosa!.');
            $this->reset();
        } catch (\Throwable $th) {
            //Emitimos mensaje de sesion 
            session()->flash('fail', $th->getMessage());
        } finally {
            $this->dispatch('action-message-locker');
        }
    }

    public function eliminar($index)
    {
        unset($this->lockers[$index]);
    }

    public function render()
    {
        return view('livewire.lockers.movimientos.transferir');
    }
}

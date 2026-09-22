<?php

namespace App\Livewire\Lockers;

use App\Constants\LockersConstants;
use App\Models\Locker;
use App\Models\LockerSocioView;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Casilleros extends Component
{
    /**
     * Propiedades para la busqueda
     */
    public array $secciones = LockersConstants::ENUM_SECCION_LOCKER;
    //Estado seleccionado del Select
    public $selected_seccion = '';
    //Texto de busqueda
    public $input_search = '';

    //Estados posibles de los lockers
    #[Locked]
    public $locker_mantenimiento_key = LockersConstants::ENUM_ESTADO_LOCKER[2];
    #[Locked]
    public $locker_disponible_key = LockersConstants::ENUM_ESTADO_LOCKER[0];
    #[Locked]
    public $locker_ocupado_key = LockersConstants::ENUM_ESTADO_LOCKER[1];
    //Estado seleccionado del buttonGroup
    public $status = LockersConstants::ENUM_ESTADO_LOCKER[1];


    #[Computed()]
    public function data_lockers()
    {
        if ($this->selected_seccion == '') {
            return [];
        }

        $consulta = LockerSocioView::where([
            ['seccion', '=', $this->selected_seccion],
            ['estado_actual', 'like', "%$this->status%"]
        ]);

        if ($this->status == $this->locker_ocupado_key) {
            $result = $consulta->whereAny(
                [
                    'numero',
                    'id_socio_actual',
                    'socio_nombre',
                    'integrante_nombre',
                    'observaciones',
                ],
                'like',
                "%$this->input_search%"
            )
                ->get();
        } else {
            $result = $consulta->where('numero', 'like', "%$this->input_search%")
                ->get();
        }
        return $result;
    }

    public function buscarCasilleros($status)
    {
        $this->status = $status;
    }


    public function render()
    {
        return view('livewire.lockers.casilleros');
    }
}

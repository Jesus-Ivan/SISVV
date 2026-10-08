<?php

namespace App\Livewire\Sistemas\Recepcion;

use App\Models\Socio;
use App\Models\SocioCuota;
use App\Services\LockerService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListaSocios extends Component
{
    use WithPagination;
    public $search;

    public function updated($search)
    {
        $this->resetPage();
    }

    //Marca a un socio eliminado utilizando SoftDeleted
    public function deleteSocio($id)
    {
        $socio = Socio::find($id);
        //Obtenemos el usuario autenticado
        $lockerService = new LockerService;
        $user = auth()->user();
        /**
         * Borrar todos los lockers del socio.
         */
        // Buscar primero los lockers (MEN) asignados al socio. en la tabla 'socios_cuotas'
        $locker_cuotas = SocioCuota::where('id_socio', $socio->id)
            ->whereNotNull('id_locker')
            ->get()->toArray();
        foreach ($locker_cuotas as $key => $cuota) {
            $lockerService->bajaLocker($cuota['id_locker'],  $user->name, 'BAJA DEL SOCIO');
        }
        $socio->delete();
    }

    //Restaura a un socio eliminado 
    public function restoreSocio($id)
    {
        $socio = Socio::withTrashed()->find($id);
        $socio->restore();
    }

    //Para socios que se encuentran eliminados, mostrar unicamente cargos pendientes
    public function generarEdoCuenta($id)
    {
        $socio = $id;
        $tipo = 'P';
        $vista = 'ORD';
        $fInicio = '2000-01-01';
        $fFin = now()->format('Y-m-d');
        $option = 's';

        return route('recepcion.estado.reporte', [
            'socio' => $socio,
            'tipo' => $tipo,
            'vista' => $vista,
            'fInicio' => $fInicio,
            'fFin' => $fFin,
            'option' => $option
        ]);
    }

    public function render()
    {
        $result = Socio::query()
            ->withTrashed()
            ->with('socioMembresia.membresia')
            ->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orwhere('apellido_p', 'like', '%' . $this->search . '%')
                    ->orwhere('apellido_m', 'like', '%' . $this->search . '%')
                    ->orWhere('id', '=', $this->search);
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.sistemas.recepcion.socios.lista-socios', [
            'listaSocios' => $result
        ]);
    }
}

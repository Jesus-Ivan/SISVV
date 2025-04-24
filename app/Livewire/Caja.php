<?php

namespace App\Livewire;

use App\Models\Caja as ModelsCaja;
use App\Models\CambioTurno;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Caja extends Component
{
    use WithPagination;
    #[Locked]
    public $codigopv;       //Propiedad publica que recibe del exterior el codigo del PV

    #[Validate('required')]
    public $puntoSeleccionado;

    #[Validate('required|numeric| min:1')]
    public $cambio;

    #[Computed]
    public function usuario()
    {
        return auth()->user();
    }

    #[Computed]
    public function puntos()
    {
        /**
         * Obtenemos todos los puntos de venta permitidos para el usuario autenticado
         * Si tiene el rol de cajero
         */
        return DB::table('users_permisos')
            ->join('puntos_venta', 'users_permisos.clave_punto_venta', '=', 'puntos_venta.clave')
            ->where('users_permisos.id_user', auth()->user()->id)
            ->where('users_permisos.clave_rol', 'CAJ')
            ->get();
    }

    #[Computed]
    public function statusCaja()
    {
        //$hoy = now();
        $unaSemanaAtras = now()->subWeek();
        //Buscamos todas las cajas abiertas en el mes, por el usuario autenticado (sin importar el punto de venta)
        return ModelsCaja::where('id_usuario', $this->usuario->id)
            ->whereBetween('fecha_apertura', [$unaSemanaAtras, now()])
            /*->whereMonth('fecha_apertura', $hoy->month)*/
            ->orderBy('fecha_apertura', 'desc')
            ->paginate(5);
    }

    public function abrirCaja()
    {
        $validated = $this->validate();
        //Buscar si el usuario trata de abrir dos veces caja, en el mismo dia, en el mismo punto
        $cajaPrevia = ModelsCaja::whereDate('fecha_apertura', '=', now()->format('Y-m-d'))
            ->where('clave_punto_venta', $this->puntoSeleccionado)
            ->where('id_usuario', $this->usuario->id)
            ->first();

        //Buscamos las cajas abiertas, en un punto determinado, en el dia actual.
        $resultCajaHoy = ModelsCaja::whereNull('fecha_cierre')
            ->where('clave_punto_venta', $this->puntoSeleccionado)
            ->whereDate('fecha_apertura', '=', now()->format('Y-m-d'))
            ->get();

        //Si no se encontro una caja previa del usuario, en el punto, en el dia actual.
        if (!$cajaPrevia) {
            //Si no hay caja abierta en el punto
            if (!count($resultCajaHoy)) {
                DB::transaction(function () use ($validated) {
                    // Format without timezone offset
                    $fechaApertura = now()->format('Y-m-d H:i:s');
                    $caja = ModelsCaja::create([
                        'fecha_apertura' => $fechaApertura,
                        'id_usuario' => $this->usuario->id,
                        'cambio_inicial' => $validated['cambio'],
                        'clave_punto_venta' => $validated['puntoSeleccionado']
                    ]);
                    //Retomamos las ventas del turno anterior (Aquellas con la columna 'corte_caja' null)
                    $this->retomarVentas($caja);
                }, 2);
                session()->flash('success', 'Caja abierta correctamente');
                $this->reset();
            } else {
                session()->flash('fail', 'Ya hay una caja abierta por otro usuario');
            }
        } else {
            session()->flash('fail', 'No puedes abrir caja dos veces');
        }
        $this->dispatch('info-caja');
    }

    public function cerrarCaja(ModelsCaja $caja)
    {
        // Format without timezone offset
        $fechaCierre = now()->format('Y-m-d H:i:s');
        try {
            //Verificamos si tiene cuentas abiertas
            if (count($this->tieneCuentasAbiertas($caja))) {
                //ABRIMOS EL MODAL PARA INFORMAR LAS CUENTAS ABIERTAS
                $this->dispatch('open-modal',  name: 'modalAdvertencia');
                return;
            }

            //Verificamos si la caja ya tenia fecha de cierre
            if ($caja->fecha_cierre) {
                //lanzamos excepcion si ya esta cerrada la caja
                throw new Exception('La caja ya esta cerrada');
            }

            //Actualizamos el estatus de la caja actual si no hay errores.
            $caja->update(['fecha_cierre' => $fechaCierre]);
            //Si la caja es diferente del punto de recepcion
            if ($caja->clave_punto_venta != 'REC') {
                //Emitimos evento para abrir el corte de caja en una pestaña nueva
                $this->dispatch('generar-corte', $caja);
            }
        } catch (\Throwable $th) {
            //Enviamos mensaje de sesion en livewire
            session()->flash('fail', $th->getMessage());
            //Emitimos evento para abrir alert
            $this->dispatch('info-caja');
        }
    }

    public function cierreParcial(ModelsCaja $caja)
    {
        // Format without timezone offset
        $fechaParcial = now()->format('Y-m-d H:i:s');
        try {
            //Verificamos si la caja ya tenia fecha de cierre
            if ($caja->fecha_cierre || $caja->cierre_parcial) {
                //lanzamos excepcion si ya esta cerrada la caja
                throw new Exception('No se puede actualizar la caja');
            }
            //Actualizamos el estatus de la caja actual si no hay errores.
            $caja->update(['cierre_parcial' => $fechaParcial]);
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            $this->dispatch('info-caja');
        }
    }

    private function tieneCuentasAbiertas($caja)
    {
        return Venta::where('corte_caja', $caja->corte)
            ->whereNull('fecha_cierre')
            ->get();
    }

    /**
     * Verifica si existio un cambio de turno, segun la fecha y la clave del punto de venta.
     * Estos datos son obtenidos de la informacion de la caja que recibe como parametro
     */
    private function retomarVentas($caja)
    {
        //Verificamos si existe algun cambio de turno en el dia actual, en el punto deseado
        $cambioTurno = CambioTurno::where('clave_punto_venta', $caja['clave_punto_venta'])
            ->whereDate('created_at', now()->toDateString())
            ->first();
        //Si existe un registro
        if ($cambioTurno) {
            //Convertimos la lista concatenada (del registro), a un array.
            $lista_folios = explode(",", $cambioTurno->payload);
            //Actualizar el corte de caja de las ventas
            Venta::whereIn('folio', $lista_folios)
                ->update([
                    'corte_caja' => $caja['corte'],
                ]);
        }
    }



    public function render()
    {
        return view('livewire.caja');
    }
}

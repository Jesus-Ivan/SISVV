<?php

namespace App\Livewire\Puntos\Ventas;

use App\Models\Caja;
use App\Models\CambioTurno;
use App\Models\PuntoVenta;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;

    public $codigopv;
    public $search;
    public $fecha;
    public $status_message;     //Mensaje para mostrar el modal

    public function mount($codigopv)
    {
        $this->codigopv = $codigopv;
        $this->fecha = now()->toDateString();
    }

    #[Computed()]
    public function ventasHoy()
    {
        return Venta::whereAny(['id_socio', 'nombre'], 'like', '%' . $this->search . '%')
            ->whereDate('fecha_apertura', $this->fecha)
            ->where('clave_punto_venta', $this->codigopv)
            ->orderby('fecha_apertura', 'desc')
            ->paginate(10);
    }

    public function refresh()
    {
        //Resetear el paginador cada vez que se busca algo
        $this->resetPage();
    }

    public function pasarVentas()
    {
        $usuario = auth()->user();          //Usuario autenticado
        //Intentamos hacer la modificacion de las ventas
        try {
            DB::transaction(function () use ($usuario) {

                //Buscamos el ultimo registro de caja abierta, con el usuario autenticado, en el punto actual.
                $caja =  Caja::whereNull('fecha_cierre')
                    ->where('id_usuario', $usuario->id)
                    ->where('clave_punto_venta', $this->codigopv)
                    ->first();
                //Si no hay caja abierta
                if (!$caja)
                    throw new Exception('No se encontro caja abierta para el usuario actual');

                //Verificamos si existe algun cambio de turno en el dia de la caja, en el punto actual
                $cambioTurno = CambioTurno::where('clave_punto_venta', $this->codigopv)
                    ->whereDate('created_at', substr($caja->fecha_apertura, 0, 10))
                    ->first();

                if ($cambioTurno)
                    throw new Exception('Ya hubo un traspaso de ventas del dia ' . substr($cambioTurno->created_at, 0, 10));

                //Buscamos la ventas abiertas con dicho corte de caja
                $ventas = Venta::where('corte_caja', $caja->corte)
                    ->whereNull('fecha_cierre')
                    ->get();
                if (!count($ventas))
                    throw new Exception('Todavia no hay ventas en tu corte');
                //Variable para concatenar los folios de ventas que se van a pasar
                $payload = "";

                //Retiramos el corte de caja de dichas ventas
                foreach ($ventas as $key => $venta) {
                    $payload = $payload . $venta->folio . ',';
                    $venta->corte_caja = null;
                    $venta->save();
                }
                //Creamos el registro del cambio de turno
                CambioTurno::create([
                    'id_user' => $usuario->id,
                    'nombre' => $usuario->name,
                    'clave_punto_venta' => $this->codigopv,
                    'payload' => $payload
                ]);
                //Cerramos caja
                $caja->fecha_cierre = now()->format('Y-m-d H:i:s');
                $caja->save();
            }, 2);
            //Emitimos evento para modificar el modal
            $this->status_message = 'Ventas asignadas al siguiente turno';
        } catch (\Throwable $th) {
            $this->status_message = $th->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.puntos.ventas.principal');
    }
}

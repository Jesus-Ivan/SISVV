<?php

namespace App\Livewire\Sistemas\Puntos;

use App\Livewire\Forms\VentaEditarForm;
use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\MotivoCorreccion;
use App\Models\PuntoVenta;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class NotasEditar extends Component
{
    //Datos generales para la correcion
    public $solicitante_id, $motivo_id;

    public $observaciones;      //Para las cortesias
    public $venta = [], $productos = [], $pagos = [];

    //Formulario de operaciones
    public VentaEditarForm $editarForm;

    //Setear el valor obtenido desde del controlador
    public function mount($folio)
    {
        //Buscar la venta
        $this->venta = Venta::find($folio)->toArray();
        //Buscar los productos de la venta
        $this->productos = DetallesVentaProducto::where('folio_venta', $folio)->get()->toArray();
        //Buscar los pagos de la venta
        $this->pagos = DetallesVentaPago::where('folio_venta', $folio)->get()->toArray();
    }

    #[Computed()]
    public function cajas()
    {
        return Caja::with('users')
            ->where('clave_punto_venta', $this->venta['clave_punto_venta'])
            ->orderBy('fecha_apertura', 'DESC')
            ->limit(35)
            ->get();
    }

    /**
     * Funcion que abre el modal de 'cajas'. En cada peticion se actualiza la propiedad computarizada que lo rellena
     */
    public function searchCajas()
    {
        //Abrir modal de cajas
        $this->dispatch('open-modal', name: 'modalCortes');
    }
    /**
     * Cambia la el corte de caja actual de la venta, por el seleccionado
     */
    public function selectCaja($corte)
    {
        //Actualizamos el nuevo corte de caja de la venta
        $this->venta['corte_caja'] = $corte;
        //Cerrar modal
        $this->dispatch('close-modal');
    }


    public function confirmarCortesia()
    {
        $validated = $this->validate([
            'observaciones' => 'required',
            'venta' => 'required|min:1',
            'solicitante_id' => 'required',
            'motivo_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                //Convertir en cortesia
                $this->editarForm->cortesia($validated['venta']['folio'], $validated['observaciones']);
                //Crear el registro de la bitacora
                $this->editarForm->registrarCorreccion($validated['venta'], $validated['solicitante_id'], $validated['motivo_id']);
            });
            //Redirigir al usuario a la pantalla principal
            $this->redirectRoute('sistemas.pv.notas');
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());    //Mensaje de session error
            $this->dispatch('open-action-message');         //abrir el alert
        } finally {
            $this->dispatch("close-modal");                 //Cerrar el dialogo de cortesia
        }
    }

    public function guardarCambios()
    {
        $validated = $this->validate([
            'venta' => 'required|min:1',
        ]);
        //Guardamos los cambios del punto de venta
        $this->editarForm->actualizarPunto(
            $validated['venta'],
            $validated['clave_punto_venta'],
            $validated['corte_caja']
        );
    }

    public function render()
    {
        $users = User::all()->toArray();
        $puntos = PuntoVenta::all()->toArray();
        $tipos_pago = TipoPago::all()->toArray();
        $motivos_correccion = MotivoCorreccion::all()->toArray();
        return view('livewire.sistemas.puntos.notas-editar', [
            'users' => $users,
            'puntos' => $puntos,
            'tipos_pago' => $tipos_pago,
            'motivos_correccion' => $motivos_correccion
        ]);
    }
}

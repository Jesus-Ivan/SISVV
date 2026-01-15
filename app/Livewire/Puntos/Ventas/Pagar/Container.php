<?php

namespace App\Livewire\Puntos\Ventas\Pagar;

use App\Livewire\Caja;
use App\Livewire\Forms\VentaForm;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\Venta;
use App\Models\Socio;
use App\Models\TipoPago;
use Dotenv\Exception\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Container extends Component
{

    public $venta;
    public $codigopv;
    public VentaForm $ventaForm;
    public $permisospv;
    public $caja;

    //Hook que se ejecuta al inicio del ciclo del vida del componente
    public function mount($folio, $codigopv, $permisospv)
    {
        //Buscar la venta junto al corte de caja
        $venta = Venta::with('caja')->find($folio);
        //Asignar la caja a la propiedad
        $this->caja = $venta->caja;

        //Guardamos los permisos del usuario en el formulario
        $this->ventaForm->permisospv = $permisospv;
        //Guardar la venta en las propiedades del componente
        $this->venta = $venta;
        $this->ventaForm->tipo_venta = $venta->tipo_venta;      //Guardamos el tipo de venta en el form
        //Si la venta es de tipo invitado del socio
        if ($venta->tipo_venta == 'invitado') {
            //guardar el socio, en el metodo del pago
            $this->ventaForm->socioPago = Socio::find($venta->id_socio);
        }
        $this->ventaForm->nombre_p_general = $venta->nombre;    //Guardamos el nombre del cliente en el form
        $this->ventaForm->nombre_invitado = $venta->nombre;     //Guardamos el nombre del INVITADO en el form
        $this->ventaForm->nombre_empleado = $venta->nombre;     //Guardamos el nombre del EMPLEADO en el form

        $this->codigopv = $codigopv;
        //Buscamos los detalles de los productos vendidos y guardamos en el formulario
        $this->ventaForm->productosTable = DetallesVentaProducto::with('catalogoProductos')
            ->where('folio_venta', $venta->folio)
            ->get()
            ->toArray();
        //Buscamos los detalles de los pagos y los guardamos en el formulario
        $this->ventaForm->pagosTable = DetallesVentaPago::where('folio_venta', $venta->folio)
            ->get()
            ->toArray();
        //Rectificamos los pagos
        $this->ventaForm->verificarPagos();
        //Despues de buscar los productos, actualizarTotal
        $this->ventaForm->actualizarTotal();
    }

    #[On('selected-socio-pago')]
    public function socioSeleccionadoPago(Socio $socio)
    {
        try {
            $this->ventaForm->setSocioPago($socio);
        } catch (\Throwable $th) {
            //Codigo de error 2, el no esta activo
            if ($th->getCode() == 2) {
                session()->flash('socioActivo', $th->getMessage());
            }
        }
    }

    #[Computed()]
    public function metodosPago()
    {
        //Si no es venta a publico general, mostrar firma
        if ($this->ventaForm->tipo_venta != 'general' && $this->ventaForm->tipo_venta != 'empleado') {
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%')
                    ->orWhere('descripcion', 'like', '%PENDI%');
            })->get();
        } else {
            //Retirar firma
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%')
                    ->orWhere('descripcion', 'like', 'FIRMA')
                    ->orWhere('descripcion', 'like', '%PENDI%');
            })->get();
        }
    }

    public function agregarPago()
    {
        try {
            //Intentamos agregar el pago seleccionado
            $this->ventaForm->agregarPago($this->metodosPago);
            //Verificar si es editable
            $this->ventaForm->verificarUltimoPago();
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Codigo de error 1, el socio no tiene firma
            if ($th->getCode() == 1) {
                session()->flash('firma', $th->getMessage());
            }
        }
    }
    public function eliminarPago($pagoIndex)
    {
        $this->ventaForm->eliminarPago($pagoIndex);
    }

    public function pagarVentaPendiente()
    {
        try {
            $this->ventaForm->pagarPendiente($this->venta);
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $this->venta->folio]);
            // Cerrar ventana
            $this->dispatch('cerrar-pagina');
        } catch (\Throwable $th) {
            //Obtener mensaje de error
            session()->flash('fail', $th->getMessage());
            //Evento para mostrar alert
            $this->dispatch('action-message-venta');
        }
    }

    public function render()
    {
        return view('livewire.puntos.ventas.pagar.container', [
            'var' => null
        ]);
    }
}

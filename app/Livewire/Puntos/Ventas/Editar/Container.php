<?php

namespace App\Livewire\Puntos\Ventas\Editar;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\DetallesVentaProducto;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\Venta;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class Container extends Component
{
    public VentaForm $ventaForm;

    #[Locked]
    public $venta;

    #[Locked]
    public $codigopv;

    #[Computed()]
    public function ventas_abiertas()
    {
        return Venta::whereNull('fecha_cierre')
            ->where([
                ["corte_caja", '=', $this->venta->corte_caja],
                ['folio', '<>', $this->venta->folio],
            ])
            ->whereAny(['id_socio', 'nombre'], 'LIKE', '%' . $this->ventaForm->seachVenta . '%')
            ->get();
    }

    //Hook que se ejecuta al inicio de vida el componente.
    public function mount(Venta $venta, $permisospv, $codigopv)
    {
        //Guardamos la instancia del modelo, correspondiente al registro de la venta(BD)
        $this->venta = $venta;
        $this->ventaForm->tipo_venta = $venta->tipo_venta;      //Guardamos el tipo de venta en el form
        //Si la venta es de tipo invitado del socio
        if ($venta->tipo_venta == 'invitado') {
            //guardar el socio, en el metodo del pago
            $this->ventaForm->socioPago = Socio::find($venta->id_socio);
        }

        $this->ventaForm->nombre_p_general = $venta->nombre;    //Guardamos el nombre del cliente en el form
        $this->ventaForm->nombre_invitado = $venta->nombre;     //Guardamos el nombre del INVITADO en el form

        //Guardamos en las propiedades del componente, el codigo del punto de venta
        $this->codigopv = $codigopv;
        //Guardamos los permisos del usuario en el formulario
        $this->ventaForm->permisospv = $permisospv;
        //Buscamos los detalles de los productos vendidos y guardamos en el formulario
        $this->ventaForm->productosTable = DetallesVentaProducto::with('catalogoProductos')
            ->where('folio_venta', $venta->folio)
            ->get()
            ->toArray();
        //Despues de buscar los productos, actualizarTotal
        $this->ventaForm->actualizarTotal();
    }

    #[On('selected-socio-pago')]
    public function socioSeleccionadoPago(Socio $socio)
    {
        try {
            $this->ventaForm->setSocioPago($socio);
        } catch (\Throwable $th) {
            //Codigo de error 1, el socio esta cancelado
            if ($th->getCode() == 2) {
                session()->flash('socioActivo', $th->getMessage());
            }
        }
    }

    #[Computed()]
    public function productosResult()
    {
        //Propiedad que almacena todos los items que coincidan con la busqueda.
        return CatalogoVistaVerde::where('nombre', 'like', '%' . $this->ventaForm->seachProduct . '%')
            ->where('clave_dpto', 'PV')
            ->whereNot('estado', 0)
            ->orderBy('nombre', 'asc')
            ->limit(40)
            ->get();
    }

    #[Computed()]
    public function metodosPago()
    {
        //Si no es venta a publico general, mostrar firma
        if ($this->ventaForm->tipo_venta != 'general') {
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%');
            })->get();
        } else {
            //Retirar firma
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%')
                    ->orWhere('descripcion', 'like', 'FIRMA');
            })->get();
        }
    }

    //hook que monitorea la actualizacion del componente
    public function updated($property)
    {
        //Si se actualizo el campo de busqueda
        if ($property === 'ventaForm.seachProduct') {
            //Limpiar los productos seleccionados previamente
            $this->ventaForm->selected = [];
        }
    }
    public function finishSelect()
    {
        try {
            //Intentamos guardar los items seleccionados, para mostrarlos en la tabla
            $this->ventaForm->agregarItems($this->productosResult);
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
        } catch (\Throwable $th) {
            dump($th->getMessage());
        }
    }

    public function updateQuantity($productoIndex, $eValue)
    {
        //Si el nuevo valor es cero o vacio
        if (!$eValue) {
            //Calcular el subtotal pero con cantidad de 1
            $this->ventaForm->calcularSubtotal($productoIndex, 1);
            return;
        }
        $this->ventaForm->calcularSubtotal($productoIndex, $eValue);
    }

    public function eliminarArticulo($productoIndex)
    {
        $this->ventaForm->eliminarArticulo($productoIndex);
    }

    public function agregarPago()
    {
        try {
            //Intentamos agregar el pago seleccionado
            $this->ventaForm->agregarPago($this->metodosPago);
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

    public function incrementar($productoIndex)
    {
        $this->ventaForm->incrementarProducto($productoIndex);
    }
    public function decrementar($productoIndex)
    {
        $this->ventaForm->decrementarProducto($productoIndex);
    }

    public function cerrarVentaExistente()
    {
        try {
            //Efectuamos los cambios para guardar la venta
            $this->ventaForm->cerrarVentaExistente($this->venta->folio, $this->codigopv);
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $this->venta->folio]);
            //redirigir al usuario
            $this->redirectRoute('pv.ventas', ['codigopv' => $this->codigopv]);
        } catch (ValidationException $th) {
            //Lanzar la excepcion de validacion a la vista
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            $this->dispatch('action-message-venta');
        }
    }

    public function guardarVentaExistente()
    {
        try {
            $this->ventaForm->guardarVentaExistente($this->venta->folio);
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $this->venta->folio]);
            //redirigir al usuario
            $this->redirectRoute('pv.ventas', ['codigopv' => $this->codigopv]);
        } catch (ValidationException $th) {
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            $this->dispatch('action-message-venta');
        }
    }

    //Abre el modal para transferir producto
    public function transferir($index_producto)
    {
        //Guardamos el indice del producto a transferir en el formulario
        $this->ventaForm->saveProductoTransferible($index_producto);
        //Evento para abrir el modal
        $this->dispatch('open-modal', name: 'modal-transferir');
    }

    //Del modal de tranferir producto, guarda el producto en una lista para mover el producto.
    public function confirmarMovimiento($folio_venta)
    {
        $this->ventaForm->agregarTransferidos($folio_venta);
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.puntos.ventas.editar.container');
    }
}

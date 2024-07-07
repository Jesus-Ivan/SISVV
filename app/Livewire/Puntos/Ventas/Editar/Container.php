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

    //Hook que se ejecuta al inicio de vida el componente.
    public function mount(Venta $venta, $permisospv, $codigopv)
    {
        //Guardamos la instancia del modelo, correspondiente al registro de la venta en ls BD
        $this->venta = $venta;
        //Guardamos en las propiedades del componente, el codigodel punto de venta
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
        return CatalogoVistaVerde::where('nombre', 'like', '%' . $this->ventaForm->seachProduct . '%')->limit(35)->get();
    }

    #[Computed()]
    public function metodosPago()
    {
        //Si no es venta a publico general, mostrar firma
        if ($this->ventaForm->tipo_venta != 'general') {
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'TRANSFERENCIA')
                    ->orWhere('descripcion', 'like', 'DEPOSITO')
                    ->orWhere('descripcion', 'like', 'CHEQUE')
                    ->orWhere('descripcion', 'like', '%SALDO%');
            })->get();
        } else {
            //Retirar firma
            return TipoPago::whereNot(function (Builder $query) {
                $query->where('descripcion', 'like', 'TRANSFERENCIA')
                    ->orWhere('descripcion', 'like', 'DEPOSITO')
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
            $this->redirectRoute('pv.ventas', ['codigopv' => $this->codigopv]);
        } catch (ValidationException $th) {
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            $this->dispatch('action-message-venta');
        }
    }

    public function render()
    {
        return view('livewire.puntos.ventas.editar.container');
    }
}

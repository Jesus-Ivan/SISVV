<?php

namespace App\Livewire\Puntos\Ventas\Nueva;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Socio;
use App\Models\TipoPago;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

class Container extends Component
{
    public VentaForm $ventaForm;
    #[Locked]
    public $codigopv;

    public function mount($codigopv, $permisospv){
        //Guardamos el codigo del pv en el componente
        $this->codigopv = $codigopv;
        //Guardamos los permisos del usuario en el formulario
        $this->ventaForm->permisospv = $permisospv;
    }

    #[On('on-selected-socio')]
    public function socioSeleccionado(Socio $socio)
    {
        $this->ventaForm->socio = $socio;
    }

    #[On('selected-socio-pago')]
    public function socioSeleccionadoPago(Socio $socio)
    {
        try {
            $this->ventaForm->setSocioPago($socio);
        } catch (\Throwable $th) {
            //Codigo de error 1, el socio no tiene firma
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

    #[Computed()]
    public function productosResult()
    {
        //Propiedad que almacena todos los items que coincidan con la busqueda.
        return CatalogoVistaVerde::where('nombre', 'like', '%' . $this->ventaForm->seachProduct . '%')->limit(35)->get();
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

    public function eliminarArticulo($productoIndex)
    {
        $this->ventaForm->eliminarArticulo($productoIndex);
    }

    public function updateQuantity($productoIndex, $eValue)
    {
        $this->ventaForm->calcularSubtotal($productoIndex, $eValue);
    }

    public function guardarVentaNueva()
    {
        try {
            //Guardamos la venta
            $this->ventaForm->guardarVentaNueva($this->codigopv);
            //Emitimos mensaje de sesion 
            session()->flash('success', 'Venta guardada correctamente');
        } catch (ValidationException $th) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
        }
        $this->dispatch('action-message-venta');
    }

    public function cerrarVentaNueva()
    {
        try {
            //Guardamos la venta completa
            $folioVenta = $this->ventaForm->cerrarVentaNueva($this->codigopv);
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $folioVenta]);
            //Mensaje de sesion para el alert
            session()->flash('success', 'Venta cerrada correctamente');
        } catch (ValidationException $th) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $th;
        } catch (\Throwable $e) {
            session()->flash('fail', $e->getMessage());
        }
        $this->dispatch('action-message-venta');
    }

    public function resetVentas()
    {
        $this->ventaForm->resetVentas();
    }


    public function render()
    {
        return view('livewire.puntos.ventas.nueva.container');
    }
}

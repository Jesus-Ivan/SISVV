<?php

namespace App\Livewire\Puntos\Ventas\Nueva;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Socio;
use App\Models\SocioMembresia;
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

    public function mount($codigopv, $permisospv)
    {
        //Guardamos el codigo del pv en el componente
        $this->codigopv = $codigopv;
        //Guardamos los permisos del usuario en el formulario
        $this->ventaForm->permisospv = $permisospv;
    }

    #[On('on-selected-socio')]
    public function socioSeleccionado(Socio $socio)
    {
        try {
            //Validamos si el socio no esta con una membresia cancelada
            $resultMembresia = SocioMembresia::where('id_socio', $socio->id)->first();
            if (!$resultMembresia) {
                throw new Exception("No se encontro membresia registrada");
            } else if ($resultMembresia->estado == 'CAN') {
                throw new Exception("Membresia de socio $socio->id cancelada");
            }
            //Si la venta es a un invitado del socio
            if ($this->ventaForm->tipo_venta == 'invitado') {
                //Repetir el socio seleccionado al principio, como socio para metodo de pago
                $this->ventaForm->socioPago = $socio;
            }
            //Guardar el socio en el form. para el header del ticket de venta
            $this->ventaForm->socio = $socio;
        } catch (\Throwable $th) {
            session()->flash('fail_socio',  $th->getMessage());
        }
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

    //hook que monitorea la actualizacion del componente
    public function updated($property, $value)
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
            $this->ventaForm->agregarPago();
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

    //Funcion que se llama, cada vez que el input de cantidad de la venta nueva, cambia
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

    public function guardarVentaNueva()
    {
        try {
            //Guardamos la venta
            $folioVenta = $this->ventaForm->guardarVentaNueva($this->codigopv);
            //Emitimos evento para abrir el ticket en nueva pestaña
            $this->dispatch('ver-ticket', ['venta' => $folioVenta]);
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

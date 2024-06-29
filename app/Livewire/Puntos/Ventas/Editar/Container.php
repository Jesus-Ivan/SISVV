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

class Container extends Component
{
    public VentaForm $ventaForm;

    #[Locked]
    public $permisospv;

    #[Locked]
    public $venta;

    #[Locked]
    public $codigopv;


    public function mount(Venta $venta)
    {
        //Guardamos la instancia del modelo, correspondiente al registro de la venta en ls BD
        $this->venta = $venta;
        //Buscamos los detalles de los productos vendidos y guardamos en la propiedad del componente
        $this->ventaForm->productosTable = DetallesVentaProducto::with('catalogoProductos')
            ->where('folio_venta', $venta->folio)
            ->get()
            ->toArray();
        //dd($this->ventaForm->productosTable);
    }

    #[On('selected-socio-pago')]
    public function socioSeleccionadoPago(Socio $socio)
    {
        $this->ventaForm->socioPago = $socio;
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
        if ($this->ventaForm->tipoVenta != 'general') {
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
            $this->ventaForm->cerrarVentaExistente($this->venta->folio);
            $this->redirectRoute('pv.ventas');
        } catch (Exception $e) {
            dump($e->getMessage());
        }
    }

    public function guardarVentaExistente()
    {

        try {
            $this->ventaForm->guardarVentaExistente($this->venta->folio);
            $this->redirectRoute('pv.ventas');
        } catch (Exception $e) {
            dump($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.puntos.ventas.editar.container');
    }
}

<?php

namespace App\Livewire\Puntos\Ventas\Nueva;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Grupos;
use App\Models\GruposModificadores;
use App\Models\Modificador;
use App\Models\ModifProducto;
use App\Models\Producto;
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

    #[Locked]
    public $producto_compuesto = null; //Almacena el producto compuesto seleccionado en el modal de productos
    #[Locked]
    public $modificadores = [];     //Almacena los posibles modificadores que el usuario puede seleccionar (de un producto compuesto)
    #[Locked]
    public $gruposModif = [];       //Almacena los grupos de modificadores (de un producto compuesto)
    public $cantidadProducto = 1;   //Utilizada en el modal de buscar productos (NEW)
    #[Locked]
    public $modal_name = 'modal-new-producto'; //Es el nombre del modal a abrir con la tecla 'ctrl' desde el front

    public function mount($codigopv, $permisospv)
    {
        //Guardamos el codigo del pv en el componente
        $this->codigopv = $codigopv;
        //Guardamos los permisos del usuario en el formulario
        $this->ventaForm->permisospv = $permisospv;
    }

    #[Computed(persist: true)]
    public function gruposModificadores()
    {
        //Buscar los grupos de modificadores
        return GruposModificadores::all();
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
    public function productosNew()
    {
        //Buscar el grupo de productos referente a los servicios de recepcion
        $gp_servicio = Grupos::where('descripcion', 'like', '%SERVICIO%')->first();
        //Preparar consulta base
        $result = Producto::where('descripcion', 'like', '%' . $this->ventaForm->seachProduct . '%')
            ->whereNot('estado', 0);
        //Si hay un grupo definido como servicio
        if ($gp_servicio) {
            $result->whereNot('id_grupo', $gp_servicio->id); //Agregar el query
        }
        return $result
            ->orderBy('descripcion', 'ASC')
            ->limit(50)
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

    public function eliminarArticulo($index_eliminar)
    {
        //Obtener el producto a eliminar
        $prod = $this->ventaForm->productosTable[$index_eliminar];
        //Realizar la eliminacion del producto, del arreglo de la tabla
        $this->ventaForm->eliminarArticulo($prod['chunk']);
        //Actualizar totales
        $this->ventaForm->recalcularSubtotales();
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

    public function seleccionarProducto($clave)
    {
        //Buscar el producto con sus modificadores y los grupos
        $producto = Producto::with(['modificador', 'grupoModif'])
            ->find($clave);
        //Validar que ingreso la cantidad
        $this->validate(
            ['cantidadProducto' => 'required|numeric'],
            [
                'cantidadProducto.required' => 'Se requiere la cantidad',
                'cantidadProducto.numeric' => 'Debe ser numerico',
            ]
        );

        try {
            //Si el producto tiene algun grupo de modificador asignado (es compuesto)
            if (count($producto->grupoModif)) {
                //Si la cantidad es negativa
                if ($this->cantidadProducto <= 0) {
                    //Lanzar excepcion
                    throw new Exception("Compuesto negativo");
                }
                $this->prepararCompuesto($producto);
                //Emitir evento para abrir el modal
                $this->dispatch('open-modal', name: $this->modal_name);
                //Emitir evento para actualizar el front de los modificadores.
                $this->dispatch('actualizar-modificadores');
            } else {
                //Agregar el producto a la tabla
                $this->ventaForm->agregarProducto($producto, $this->cantidadProducto, time(), true);
                //Actualizar el total de la venta
                $this->ventaForm->recalcularSubtotales();
                //Limpiar las propiedades auxiliares
                $this->reset('producto_compuesto', 'modificadores', 'gruposModif', 'cantidadProducto');
                //Emitimos evento para cerrar el componente del modal
                $this->dispatch('close-modal');
            }
        } catch (ValidationException $th) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $th;
        } catch (Exception $e) {
            //Mensaje de sesion para el error (no alert)
            session()->flash('fail', $e->getMessage());
        }
    }

    /**
     * Prepara el modal para los productos compuestos
     */
    public function prepararCompuesto($producto)
    {
        //Establer las propiedades de produto compuesto, en el componente 
        $this->producto_compuesto = $producto->toArray();
        $this->modificadores = $producto->modificador->toArray();
        $this->gruposModif = $producto->grupoModif->toArray();

        //Agregar descripcion del producto (modificadores posibles)
        foreach ($this->modificadores as $index => $modif) {
            $result = Producto::find($modif['clave_modificador']);
            $this->modificadores[$index]['descripcion'] = $result->descripcion;
        }
        //Agregar descripcion del grupo de modificadores
        foreach ($this->gruposModif as $index => $grupo) {
            $grupo = $this->gruposModificadores->find($grupo['id_grupo']);
            if ($grupo) {
                $this->gruposModif[$index]['descripcion'] = $grupo->descripcion;
            } else {
                $this->gruposModif[$index]['descripcion'] = 'N/A';
            }
        }
        //Actualizar el nombre del modal a abrir con el evento 'ctrl' desde el front
        $this->modal_name = 'modal-modificadores';
    }

    public function guardarCompuesto($selected)
    {
        //Buscar el producto compuesto
        $producto = Producto::find($this->producto_compuesto['clave']);
        //Generar timeStamp
        $time = time();
        //Agregar el producto a la tabla
        $this->ventaForm->agregarProducto($producto, $this->cantidadProducto, $time);
        //Agregar los modificadores a la tabla 
        $this->ventaForm->agregarModificadores($selected, $time);
        //Actualizar el total de la venta
        $this->ventaForm->recalcularSubtotales();
    }

    public function limpiarCompuesto()
    {
        //Limpiar las propiedades auxiliares
        $this->reset('producto_compuesto', 'modificadores', 'gruposModif', 'cantidadProducto', 'modal_name');
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }


    public function render()
    {
        return view('livewire.puntos.ventas.nueva.container', [
            'var' => null
        ]);
    }
}

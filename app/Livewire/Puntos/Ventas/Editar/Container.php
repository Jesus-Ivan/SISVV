<?php

namespace App\Livewire\Puntos\Ventas\Editar;

use App\Livewire\Forms\VentaForm;
use App\Models\CatalogoVistaVerde;
use App\Models\ConceptoCancelacion;
use App\Models\DetallesVentaProducto;
use App\Models\GruposModificadores;
use App\Models\Producto;
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

    #[Locked]
    public $producto_compuesto = null; //Almacena el producto compuesto seleccionado en el modal de productos
    #[Locked]
    public $modificadores = [];     //Almacena los posibles modificadores que el usuario puede seleccionar (de un producto compuesto)
    #[Locked]
    public $gruposModif = [];       //Almacena los grupos de modificadores (de un producto compuesto)
    public $cantidadProducto = 1;   //Utilizada en el modal de buscar productos (NEW)
    #[Locked]
    public $modal_name = 'modal-new-producto'; //Es el nombre del modal a abrir con la tecla 'ctrl' desde el front

    //Utilizados en el modal de eliminacion de articulo
    public $id_eliminacion = null, $motivo = null;

    #[Computed(persist: true)]
    public function gruposModificadores()
    {
        //Buscar los grupos de modificadores
        return GruposModificadores::all();
    }

    #[Computed(persist: true)]
    public function conceptos()
    {
        //Buscar los conceptos validos para eliminacion
        return ConceptoCancelacion::all();
    }

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
        //Buscamos los detalles de los productos vendidos
        $result = DetallesVentaProducto::where('folio_venta', $venta->folio)
            ->get()
            ->toArray();

        //Agregamos el par clave-valor a los modificadores (no se almacena en la BD)
        $modif = array_map(function ($producto) {
            //Si el producto empieza con el caracter ">"
            if (preg_match("/^>/i", $producto['nombre'])) {
                $producto['modif'] = true;
            }
            return $producto;
        }, $result);

        $this->ventaForm->productosTable  = $modif; //Actualizamos la tabla
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
    public function productosNew()
    {
        $result = Producto::where('descripcion', 'like', '%' . $this->ventaForm->seachProduct . '%')
            ->whereNot('estado', 0)
            ->orderBy('descripcion', 'asc')
            ->limit(50)
            ->get();
        return $result;
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

    public function eliminarArticulo($index)
    {
        //Obtener el producto a eliminar
        $prod = $this->ventaForm->productosTable[$index];
        //Si cuenta con un id (de la BD)
        if (array_key_exists('id', $prod)) {
            //Guardar el index del item a eliminar
            $this->ventaForm->indexSeleccionado = $index;
            //Abrir modal de eliminacion
            $this->dispatch('open-modal', name: 'modal-motivo eliminacion');
        } else {
            //Realizar la eliminacion del producto, del arreglo de la tabla
            $this->ventaForm->eliminarArticulo($prod['chunk']);
            //Actualizar totales
            $this->ventaForm->recalcularSubtotales();
        }
    }

    public function confirmarEliminacion()
    {
        $rules = [
            'id_eliminacion' => 'required'
        ];
        $messages = [
            'id_eliminacion.required' => 'Seleccione motivo'
        ];
        $concepto = $this->conceptos->find($this->id_eliminacion);
        //Si esta selecionado un concepto editable
        if ($concepto && $concepto->editable) {
            $rules['motivo'] = 'required';
            $messages['motivo.required'] = 'Especifique motivo';
        };
        //Validar el 
        $this->validate($rules, $messages);
        //Obtener el producto a eliminar
        $prod = $this->ventaForm->productosTable[$this->ventaForm->indexSeleccionado];
        //Realizar la eliminacion
        $this->ventaForm->eliminarArticuloBD($prod['chunk'], $this->id_eliminacion, $this->motivo);
        //Calcular los totales
        $this->ventaForm->recalcularSubtotales();
        $this->reset('id_eliminacion', 'motivo');
        $this->dispatch('close-modal');
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
                $this->ventaForm->actualizarTotal();
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
            session()->flash('fail_cantidad', $e->getMessage());
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
        //Buscar el producto compuesto, segun el primer modificador seleccionado
        $producto = Producto::find(reset($selected)['clave_producto']);
        //Generar timeStamp
        $time = time();
        //Agregar el producto a la tabla
        $this->ventaForm->agregarProducto($producto, $this->cantidadProducto, $time);
        //Agregar los modificadores a la tabla 
        $this->ventaForm->agregarModificadores($selected, $time);
        //Actualizar el total de la venta
        $this->ventaForm->actualizarTotal();
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
        return view('livewire.puntos.ventas.editar.container');
    }
}

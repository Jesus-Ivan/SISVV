<?php

namespace App\Livewire\Recepcion\Ventas\Nueva;

use App\Models\CatalogoVistaVerde;
use App\Models\Grupos;
use App\Models\Producto;
use Dotenv\Exception\ValidationException;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ProductosModalBody extends Component
{
    public $searchProduct;          //Almacenamos el campo de busqueda
    public $selectedProducts = [];  //Almacenamos los productos seleccionados [1 => false, 10 => true ....]
    public $cantidadProducto = 1;

    #[Computed()]
    public function productosNew()
    {
        //Buscar el grupo de productos referente a los servicios de recepcion
        $gp_servicio = Grupos::where('descripcion', 'like', '%SERVICIO%')->first();
        //Preparar consulta base
        $result = Producto::where('descripcion', 'like', '%' . $this->searchProduct . '%')
            ->whereNot('estado', 0);
        //Si hay un grupo definido como servicio
        if ($gp_servicio) {
            $result->where('id_grupo', $gp_servicio->id); //Agregar el query
        }
        return $result
            ->orderBy('descripcion', 'asc')
            ->limit(50)
            ->get();
    }

    //Metodo que se ejecuta para guardar los elementos seleccionados
    public function finalizarSeleccion()
    {
        //Filtramos los productos seleccionados, cuyo valor sea true
        $total_seleccionados = array_filter($this->selectedProducts, function ($val) {
            return $val;
        });
        //Si ha seleccionado al menos 1 articulo
        if (count($total_seleccionados) > 0) {
            //Emitimos evento con los productos seleccionados, al resto de componentes
            $this->dispatch('productosSeleccionados', $total_seleccionados);
            //Emitimos evento para cerrar el componente del modal
            $this->dispatch('close-modal');
            //Reseteamos el componente
            $this->reset();
        }
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
                //Emitimos evento con el producto seleccionado
                $this->dispatch('producto-seleccionado', $producto, $this->cantidadProducto);
                //Actualizar el total de la venta
                //$this->ventaForm->recalcularSubtotales();
                //Limpiar las propiedades
                $this->reset('searchProduct', 'selectedProducts', 'cantidadProducto');
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

    public function render()
    {
        return view('livewire.recepcion.ventas.nueva.productos-modal-body');
    }
}

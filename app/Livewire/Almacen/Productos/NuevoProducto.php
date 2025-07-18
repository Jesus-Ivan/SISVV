<?php

namespace App\Livewire\Almacen\Productos;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\ProductoForm;
use App\Models\Grupos;
use App\Models\GruposModificadores;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Subgrupos;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevoProducto extends Component
{
    public ProductoForm $form;

    #[Computed()]
    public function subgrupos()
    {
        return Subgrupos::where('id_grupo', $this->form->id_grupo)
            ->get();
    }

    #[Computed()]
    public function grupos()
    {
        $result = Grupos::where('tipo', AlmacenConstants::PRODUCTOS_KEY)
            ->get();
        return $result;
    }

    #[On('selected-receta')]
    public function onSelectedReceta($clave_insumo)
    {
        $insumo = Insumo::with('unidad')
            ->where('clave', $clave_insumo)
            ->first();
        $this->form->agregarInsumoReceta($insumo);
    }
    public function eliminarInsumo($index)
    {
        //Eliminar el insumo de la receta
        $this->form->eliminarInsumoReceta($index);
    }

    #[On('selected-grupo')]
    public function onSelectedGrupo($id_grupo_insumo)
    {
        //Buscar el grupo de modificador
        $grupo = GruposModificadores::find($id_grupo_insumo);
        //si existe
        if ($grupo) {
            //Agregarlo al array del formulario
            $this->form->agregarGrupoModificador($grupo);
        }
    }
    public function eliminarGrupo($index)
    {
        //Eliminar el grupo de modificador
        $this->form->eliminarGrupo($index);
    }

    #[On('selected-producto')]
    public function onSelectedProducto($clave)
    {
        //Buscar el producto (posible modificador)
        $producto = Producto::find($clave);
        //si existe
        if ($producto) {
            //Agregarlo al array del formulario
            $this->form->agregarProducto($producto);
        }
    }

    public function eliminarProductoModif($index)
    {
        //Eliminar el producto
        $this->form->eliminarProducto($index);
    }

    public function actualizarTotal()
    {
        $this->form->recalcularSubtotales();
    }

    public function guardar()
    {
        try {
            DB::transaction(function () {
                //Crear el producto
                $result = $this->form->crearProducto();
                //Crea las propiedades de la receta del producto nuevo
                $this->form->crearReceta($result);
                //Crea las propiedades de producto compuesto
                $this->form->crearCompuesto($result);
                //Limpiar el formulario
                $this->form->limpiar();
            }, 2);
            //Mensage de session para el alert
            session()->flash('success', 'Producto registrado correctamente');
            //Evento para abrir el alert
            $this->dispatch('open-action-message');
        } catch (ValidationException $th) {
            //Lanzar la excepcion de validacion a la vista
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            //Evento para abrir el alert
            $this->dispatch('open-action-message');
        }
    }

    public function changedPrecio()
    {
        $this->form->calcularPrecioIva();
    }

    public function changedIva()
    {
        $this->form->calcularPrecioIva();
    }
    public function changedPrecioIva()
    {
        $this->form->calcularPrecioSinIva();
    }

    public function render()
    {
        return view('livewire.almacen.productos.nuevo-producto');
    }
}

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

class EditarProducto extends Component
{

    public ProductoForm $form;

    public function mount($clave)
    {
        //Buscar el producto
        $producto = Producto::find($clave);
        if ($producto) {
            //Setar los valores editables en el form
            $this->form->setValues($producto);
        } else {
            //redirigir al usuario en caso de no existir la presentacion
            $this->redirectRoute('almacen.productos');
        }
    }

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
                //Guardar cambios del producto
                $this->form->actualizarProducto();
            }, 2);
            //En caso de exito, redirigir al usuario.
            $this->redirectRoute('almacen.productos');
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
        return view('livewire.almacen.productos.editar-producto');
    }
}

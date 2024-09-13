<?php

namespace App\Livewire\Almacen\Ordenes;

use App\Models\CatalogoVistaVerde;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Models\UnidadCatalogo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevaOrden extends Component
{
    //Lista de los articulos agregados a la orden de compra
    public array $lista_articulos;
    //El articulo que se selecciona.
    public $articulo_seleccionado;
    //Propiedades auxiliares para el modal de agregar articulos a la orden
    public $cantidad = 0, $costo_unitario = 0, $iva = false, $iva_cant = 0, $id_proveedor;
    public $id_unidad = null;


    public $tipo_orden;

    #[On('selected-articulo')]
    public function onSelectedArticulo($codigo)
    {
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'cantidad', 'costo_unitario', 'iva', 'iva_cant', 'id_proveedor', 'id_unidad');
        //buscamos el articulo
        $result = CatalogoVistaVerde::find($codigo);
        if ($result) {
            $this->articulo_seleccionado = $result;
            $this->cantidad = 1;                                //Establecemos en 1 la cantidad
            $this->id_proveedor = $result->id_proveedor;
        }
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    //Esta propiedad se ocupa unicamente para obtener las unidades disponibles para el articulo seleccionado
    #[Computed()]
    public function unidadesArticulo()
    {
        try {
            return UnidadCatalogo::with('unidad')
                ->where('codigo_catalogo', $this->articulo_seleccionado->codigo)
                ->get();
        } catch (\Throwable $th) {
            return [];
        }
    }

    //Esta propiedad se ocupa para los nombres de las unidades de la tabla
    #[Computed()]
    public function unidades()
    {
        return Unidad::all();
    }

    public function changeUnidad($eValue)
    {
        //Verificamos si el valor recibido del front es null
        if ($eValue) {
            $result = $this->unidadesArticulo->where('id_unidad', $eValue)->first();    //Buscamos la unidad relacionada con el articulo
            $this->costo_unitario = $result->costo;                             //Guardamos el costo por unidad
        } else {
            $this->costo_unitario = 0;                                          //limpiamos el costo
        }
        $this->id_unidad = $eValue;                                         //Guardamos la unidad seleccionada
    }

    public function agregarArticulo()
    {
        //Si hay un articulo seleccionado
        if ($this->articulo_seleccionado) {
            //validamos propiedades
            $validated = $this->validate([
                'cantidad' => 'required|numeric',
                'costo_unitario' => 'required|numeric',
                'id_unidad' => 'required',
                'id_proveedor'=>'required'
            ]);
            //Convertir el objeto a array
            $articulo = $this->articulo_seleccionado->toArray();
            /*
            Actualizamos las llaves del array
            */
            $articulo['cantidad'] = $this->cantidad;
            $articulo['costo_unitario'] = $this->costo_unitario;
            $articulo['iva'] = $this->iva;
            $articulo['iva_cant'] = $this->iva_cant;
            $articulo['importe'] = $this->costo_unitario * $this->cantidad;
            $articulo['id_proveedor'] = $this->id_proveedor;
            $articulo['id_unidad'] = $this->id_unidad;

            //agregamos a la lista
            $this->lista_articulos[] = $articulo;
            //limpiamos propiedades
            $this->reset('articulo_seleccionado', 'cantidad', 'costo_unitario', 'iva', 'iva_cant', 'id_proveedor', 'id_unidad');
        }
    }

    public function cancelar(){
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'cantidad', 'costo_unitario', 'iva', 'iva_cant', 'id_proveedor', 'id_unidad');
        //emitir evento
        $this->dispatch('close-modal');
    }

    public function eliminarArticulo($indexArticulo)
    {
        //Eliminamos el articulo de la lista
        unset($indexArticulo, $this->lista_articulos);
    }

    public function guardarOrden()
    {
        $validated = $this->validate([
            'tipo_orden' => 'required',
            'lista_articulos' => 'required|array|min:1',
        ]);
    }
    public function render()
    {
        return view('livewire.almacen.ordenes.nueva-orden');
    }
}

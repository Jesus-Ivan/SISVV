<?php

namespace App\Livewire\Almacen\Ordenes;

use App\Models\CatalogoVistaVerde;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Models\UnidadCatalogo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class NuevaOrden extends Component
{
    //Lista de los articulos agregados a la orden de compra
    public array $lista_articulos;
    public $tipo_orden;         //Tipo de orden a registrar
    //El articulo (original) que se selecciona en el modal.
    public $articulo_seleccionado;
    //Propiedades auxiliares para el modal de agregar articulos a la orden
    public $cantidad = 0, $costo_unitario = 0, $iva = false, $iva_cant = 0, $id_proveedor, $id_unidad = null;
    //Fecha de hoy, para mostrar en la vista y en el registro de la orden de compra
    public $hoy;
    /** 
     * Propiedades para la edicion en tabla
     */
    public $index_articulo = -1, $articulo_editando = null;


    public function mount()
    {
        $this->hoy = now();
    }



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

    public function updatedIva($value)
    {
        if (! $value) {
            $this->iva_cant = 0;
        }
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
                'cantidad' => 'required|numeric|min:0.01',
                'costo_unitario' => 'required|numeric|min:0.01',
                'id_unidad' => 'required',
                'id_proveedor' => 'required'
            ]);
            //Convertir el objeto a array
            $articulo = $this->articulo_seleccionado->toArray();
            /*
            Actualizamos las llaves del array
            */
            $articulo['cantidad'] =  $validated['cantidad'];
            $articulo['costo_unitario'] =  $validated['costo_unitario'];
            $articulo['iva_cant'] = $this->iva_cant;
            $articulo['importe'] = $validated['cantidad'] * $validated['costo_unitario'];
            $articulo['id_proveedor'] =  $validated['id_proveedor'];
            $articulo['id_unidad'] =  $validated['id_unidad'];
            $articulo['consultado'] = now();

            //agregamos a la lista
            $this->lista_articulos[] = $articulo;
            //limpiamos propiedades
            $this->reset('articulo_seleccionado', 'cantidad', 'costo_unitario', 'iva', 'iva_cant', 'id_proveedor', 'id_unidad');
        }
    }

    public function cancelar()
    {
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'cantidad', 'costo_unitario', 'iva', 'iva_cant', 'id_proveedor', 'id_unidad');
        //emitir evento
        $this->dispatch('close-modal');
    }

    public function eliminarArticulo($indexArticulo)
    {
        //Eliminamos el articulo de la lista
        unset($this->lista_articulos[$indexArticulo]);
    }

    public function guardarOrden()
    {
        try {
            $this->registrarOrden();        //Registramos en la BD
            session()->flash('success-compra', 'Orden registrada correctamente');   //Mensaje de sesion
            $this->reset();                 //Limpiamos el componente completo
            $this->hoy = now();             //Reestablecemos la fecha
        } catch (ValidationException $e) {
            throw $e;                       //Si es excepcion de validacion, lanzar a la vista
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());    //Mensaje de sesion de error
        }
        $this->dispatch('compra');                          //Emitimos evento para mostrar el message-alert
    }

    public function editarArticulo($index)
    {
        //Guardamos el indice (del array) del articulo que se planea editar
        $this->index_articulo = $index;
        //Creamos copia del articulo, para el modo edicion
        $this->articulo_editando = $this->lista_articulos[$index];
    }

    public function cancelarEdicion()
    {
        $this->index_articulo = -1;
        //$this->articulo_editando = null;
    }

    public function confirmarEdicion($index)
    {
        //Actualizar la cantidad
        $this->lista_articulos[$index]['cantidad'] = $this->articulo_editando['cantidad'];
        //Actualizar el costo unitario
        $this->lista_articulos[$index]['costo_unitario'] = $this->articulo_editando['costo_unitario'];
        //Actualizar el importe
        $this->lista_articulos[$index]['importe'] = $this->articulo_editando['cantidad'] * $this->articulo_editando['costo_unitario'];
        //Actualizar el iva
        $this->lista_articulos[$index]['iva_cant'] = $this->articulo_editando['iva_cant'];
        //Salir del modo edicion
        $this->cancelarEdicion();
    }

    private function registrarOrden()
    {
        $validated = $this->validate([
            'tipo_orden' => 'required',
            'lista_articulos' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = array_sum(array_column($validated['lista_articulos'], 'importe'));
            $iva = array_sum(array_column($validated['lista_articulos'], 'iva_cant'));
            //creamos la orden
            $result_orden = OrdenCompra::create([
                'fecha' => $this->hoy,
                'tipo_orden' => $validated['tipo_orden'],
                'id_user' => auth()->user()->id,
                'cantidad' => count($validated['lista_articulos']),
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $subtotal + $iva,
            ]);
            foreach ($validated['lista_articulos'] as $key => $articulo) {
                //creamos los detalles de la orden de compra
                DB::table('detalles_compras')->insert([
                    'folio_orden' => $result_orden->folio,
                    'codigo_producto' => $articulo['codigo'],
                    'nombre' => $articulo['nombre'],
                    'id_unidad' => $articulo['id_unidad'],
                    'cantidad' => $articulo['cantidad'],
                    'costo_unitario' => $articulo['costo_unitario'],
                    'id_proveedor' => $articulo['id_proveedor'],
                    'importe' => $articulo['importe'],
                    'iva' => $articulo['iva_cant'],
                    'subtotal' => 0.0,
                    'almacen' => $articulo['stock_amc'],
                    'bar' => $articulo['stock_bar'],
                    'barra' => $articulo['stock_barra'],
                    'caddie' => $articulo['stock_caddie'],
                    'cafeteria' => $articulo['stock_caf'],
                    'cocina' => 0.0,
                    'consultado' => $articulo['consultado']
                ]);
            }
        }, 2);
    }
    public function render()
    {
        return view('livewire.almacen.ordenes.nueva-orden');
    }
}

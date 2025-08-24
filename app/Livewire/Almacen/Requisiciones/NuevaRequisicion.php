<?php

namespace App\Livewire\Almacen\Requisiciones;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\RequisicionesForm;
use App\Models\Presentacion;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class NuevaRequisicion extends Component
{
    //El articulo (original) que se selecciona en el modal.
    public $articulo_seleccionado;
    //Propiedades auxiliares para el modal de agregar articulos a la orden
    public $costo_unitario = 0, $iva = 16, $costo_con_impuesto = 0, $id_proveedor, $id_grupo = null;
    //Fecha de hoy, para mostrar en la vista y en el registro de la orden de compra
    public $hoy;
    public $tittle = '';

    public RequisicionesForm $form;


    public function mount()
    {
        //inicializamos la fecha de hoy
        $this->hoy = now();
    }

    #[On('selected-presentacion')]
    public function onSelectedArticulo($clave)
    {
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'costo_unitario', 'iva', 'costo_con_impuesto', 'id_proveedor');
        //buscamos el articulo
        $result = Presentacion::find($clave);
        if ($result) {
            //Actualizar el articulo seleccionado
            $this->articulo_seleccionado = $result->toArray();
            //actualizar el costo unitario
            $this->costo_unitario = $result->costo;
            //actualizar el iva
            $this->iva = $result->iva;
            //actualizar el costo con impuesto
            $this->costo_con_impuesto = $result->costo_con_impuesto;
            $this->id_proveedor = $result->id_proveedor;
        }
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        return $result;
    }

    public function agregarArticulo()
    {
        //validamos propiedades
        $validated = $this->validate([
            'articulo_seleccionado' => 'required',
            'id_proveedor' => 'required',
            'costo_unitario' => 'required|numeric',
            'iva' => 'required|numeric',
            'costo_con_impuesto' => 'required|numeric',
        ]);

        //agregamos a la lista
        $this->form->agregarPresentacion($validated);
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'costo_unitario', 'iva', 'costo_con_impuesto', 'id_proveedor');
    }

    public function cancelar()
    {
        //limpiamos propiedades
        $this->reset('articulo_seleccionado', 'costo_unitario', 'iva', 'costo_con_impuesto', 'id_proveedor');
        //emitir evento
        $this->dispatch('close-modal');
    }

    public function eliminarArticulo($indexArticulo)
    {
        //Eliminamos el articulo de la lista
        $this->form->eliminarPresentacion($indexArticulo);
    }

    public function guardarOrden()
    {
        try {
            DB::transaction(function () {
                //Crear la nueva orden
                $this->form->crearRequisicion();
            }, 2);
            session()->flash('success-compra', 'Orden registrada correctamente');   //Mensaje de sesion
            $this->hoy = now();             //Reestablecemos la fecha
        } catch (ValidationException $e) {
            throw $e;                       //Si es excepcion de validacion, lanzar a la vista
        } catch (Throwable $th) {
            session()->flash('fail', $th->getMessage());    //Mensaje de sesion de error
        }
        $this->dispatch('compra');                          //Emitimos evento para mostrar el message-alert
    }

    //Utilizado para actualizar el precio desde el modal
    public function calcularPrecioIva()
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->iva) == 0)
            $this->iva = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->costo_unitario) == 0)
            $this->costo_unitario = '0';
        //Calcular costo con iva
        $costo_iva = $this->costo_unitario + ($this->costo_unitario * ($this->iva / 100));
        $this->costo_con_impuesto = round($costo_iva, 2);
    }
    //Utilizado para actualizar el precio sin iva desde el modal
    public function calcularPrecioSinIva()
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->costo_con_impuesto) == 0)
            $this->costo_con_impuesto = '0';
        //Calcular Costo sin iva
        $costo_sin_iva = ($this->costo_con_impuesto * 100) / (100 + $this->iva);
        $this->costo_unitario = round($costo_sin_iva, 2);
    }

    //Utilizado para actualizar el importe total desde la tabla
    public function actualizarImporte($index)
    {
        $this->form->actualizarImporte($index);
    }

    //Utilizado para actualizar el costo desde la tabla
    public function actualizarCostoIva($index)
    {
        $this->form->actualizarCostoIva($index);
        $this->form->actualizarImporte($index);
    }

    //Utilizado para actualizar el costo sin iva desde la tabla
    public function actualizarCostoSinIva($index)
    {
        $this->form->actualizarCostoSinIva($index);
        $this->form->actualizarImporte($index);
    }

    public function render()
    {
        return view('livewire.almacen.requisiciones.nueva-requisicion');
    }
}

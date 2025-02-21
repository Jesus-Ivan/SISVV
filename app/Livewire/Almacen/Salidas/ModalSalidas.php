<?php

namespace App\Livewire\Almacen\Salidas;

use App\Constants\AlmacenConstants;
use App\Models\CatalogoVistaVerde;
use App\Models\Stock;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSalidas extends Component
{
    #[Modelable]
    public $clave_stock_origen;

    public $articulo_seleccionado;
    #[Locked]
    public $cantidad_stock;
    #[Locked]
    public $peso_stock;

    public $cantidad_salida;
    public $peso_salida;
    public $precio_venta;
    public $monto = 0;

    #[On('selected-articulo')]
    public function onSelectedInput(CatalogoVistaVerde $data)
    {
        //Almacenar el articulo seleccionado
        $this->articulo_seleccionado = $data->toArray();
        //Almacenar el costo del precio para vender (que es lo mismo de la columna 'costo_unitario' en la tabla 'catalogo_vista_verde')
        $this->precio_venta = $data->costo_unitario;
        //Limpiar los inputs de cantidad, peso
        $this->reset('cantidad_salida', 'peso_salida');
        $this->cantidad_stock = Stock::where('codigo_catalogo', $data->codigo)
            ->where("tipo", AlmacenConstants::CANTIDAD_KEY)
            ->first();
        $this->peso_stock =  Stock::where('codigo_catalogo', $data->codigo)
            ->where("tipo", AlmacenConstants::PESO_KEY)
            ->first();
    }

    public function changedPrecio($eValue)
    {
        if ($this->articulo_seleccionado) {
            if (! $eValue) {
                $this->precio_venta = 0;
            } else {
                $this->precio_venta = $eValue;
            }
            $this->actualizarMonto();
        }
    }

    public function actualizarMonto()
    {
        if ($this->articulo_seleccionado) {
            if ($this->peso_salida) {
                $this->monto = $this->peso_salida * $this->precio_venta;
            } elseif ($this->cantidad_salida) {
                $this->monto = $this->cantidad_salida * $this->precio_venta;
            } else {
                $this->monto = 0;
            }
        }
    }

    public function agregarSalida()
    {
        //Validamos que haya seleccionado un articulo
        $validated = $this->validate([
            'articulo_seleccionado' => 'required'
        ]);
        //Agregamos el stock de cantidad (unitario)
        $validated['cantidad_stock'] = $this->cantidad_stock;
        //Agregamos el stock de peso (peso)
        $validated['peso_stock'] = $this->peso_stock;

        //Verificamos si selecciono previamente la bodega de origen
        if (!$this->clave_stock_origen) {
            session()->flash('error_modal', 'Selecciona bodega de origen');
            return;
        }
        //Verificamos si selecciono la bodega de salida
        if (is_null($this->cantidad_salida) && is_null($this->peso_salida)) {
            session()->flash('error_modal', 'Debes ingresar peso o cantidad');
            return;
        }
        //Verificamos no existe ningun stock registrado
        if (is_null($validated['cantidad_stock']) && is_null($validated['peso_stock'])) {
            session()->flash('error_modal', 'No hay registro de stock en la BD');
            return;
        }

        //Emitimos evento para agregar la salida
        $this->dispatch('añadirSalida', [
            'codigo' => $this->articulo_seleccionado['codigo'],
            'nombre' => $this->articulo_seleccionado['nombre'],
            'cantidad_salida' => $this->cantidad_salida,
            'peso_salida' => $this->peso_salida,
            'cantidad_origen' => $this->cantidad_stock ? $this->cantidad_stock[$this->clave_stock_origen] : null,
            'peso_origen' => $this->peso_stock ? $this->peso_stock[$this->clave_stock_origen] : null,
            'costo_unitario' => $this->precio_venta,
            'monto' => $this->monto
        ]);
        //$this->dispatch('close-modal'); //Emitimos evento para cerrar el componente del modal
        $this->reset('articulo_seleccionado', 'cantidad_stock', 'peso_stock', 'cantidad_salida', 'peso_salida','monto','precio_venta');
    }

    public function render()
    {
        return view('livewire.almacen.salidas.modal-salidas');
    }
}

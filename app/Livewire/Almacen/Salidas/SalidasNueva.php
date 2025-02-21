<?php

namespace App\Livewire\Almacen\Salidas;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\DetallesSalida;
use App\Models\InventarioPrincipal;
use App\Models\Salida;
use App\Models\Stock;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SalidasNueva extends Component
{
    #[Validate('required')]
    public $articulos = [];

    #[Validate('required')]
    public $fechaActual;

    #[Validate('max:150')]
    public $observaciones;

    #[Validate('required')]
    public $clave_origen;

    #[Validate('required')]
    public $clave_destino;

    #[Computed()]
    public function bodegas_origen()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function bodegas_destino()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_EXTER_KEY)->get();
    }

    #[On('añadirSalida')]
    public function añadir($articulo)
    {
        $this->articulos[] = $articulo;
    }


    public function changedOrigen()
    {
        //Limipiar la lista de articulos
        $this->reset('articulos');
    }

    //ELIMINAR ARTICULO DE LA LISTA DE SALIDA
    public function remove($articuloIndex)
    {
        unset($this->articulos[$articuloIndex]);
    }

    //MOSTRAR FECHA
    public function mount()
    {
        $this->fechaActual = now()->format('Y-m-d');
    }

    //CONFIRMAR Y RESTAR SALIDA DEL STOCK
    public function confirmarSalida()
    {
        $info = $this->validate();

        try {
            //Crear la salida
            DB::transaction(function () use ($info) {
                $resultSalida = Salida::create([
                    'user_name' => auth()->user()->name,
                    'clave_origen' => $info['clave_origen'],
                    'clave_destino' => $info['clave_destino'],
                    'observaciones' => $info['observaciones'],
                    'fecha' => $this->fechaActual,
                    'monto' => array_sum(array_column($info['articulos'], 'monto'))
                ]);
                //REGISTRAMOS LOS DETALLES DE LA SALIDA EN LA TABLA CORRESPONDIENTE
                foreach ($info['articulos'] as $key => $articulo) {
                    DetallesSalida::create([
                        'folio_salida' => $resultSalida->folio,
                        'codigo_articulo' => $articulo['codigo'],
                        'nombre' => $articulo['nombre'],
                        'stock_origen_cantidad' => $articulo['cantidad_origen'],
                        'stock_origen_peso' => $articulo['peso_origen'],
                        'cantidad_salida' => $articulo['cantidad_salida'],
                        'peso_salida' => $articulo['peso_salida'],
                        'costo_unitario' => $articulo['costo_unitario'],
                        'monto' => $articulo['monto'],
                    ]);
                }
                //RESTAMOS Y ACTUALIZAMOS EL STOCK DEL INVENTARIO PRINCIPAL
                $this->actualizarStock($info['articulos'], $info['clave_origen']);
            });
            //MENSAJE DE ALERTA
            session()->flash('success', "Salida registrada con exito");
            //RESETEAMOS LOS VALORES
            $this->reset(['articulos', 'observaciones', 'clave_origen', 'clave_destino']);
            //Restablecemos la fecha
            $this->fechaActual = now()->format('Y-m-d');
        } catch (\Throwable $th) {
            //MENSAJE DE ALERTA
            session()->flash('fail', $th->getMessage());
        } finally {
            $this->dispatch('open-action-message');
        }
    }

    /**
     * Aumenta o disminuye los stocks de la lista
     */
    private function actualizarStock($lista_articulos, $clave_origen)
    {
        foreach ($lista_articulos as $key => $articulo) {
            //Buscar los stocks del articulo
            $stock = Stock::where('codigo_catalogo', $articulo['codigo'])->get();
            //Si no hay stocks
            if (!count($stock)) throw new Exception("El articulo: " . $articulo['nombre'] . ", no cuenta con ningun registro de stock", 1);

            //Si la cantidad del articulo que se desea traspasar es mayor a cero
            if ($articulo['cantidad_salida'] > 0) {
                //Buscar el stock unitario
                $stock_cantidad = $stock->where('tipo', AlmacenConstants::CANTIDAD_KEY)->first();
                //Si no tiene stock de cantidad (unitario)
                if (!$stock_cantidad) throw new Exception("No hay stock " . AlmacenConstants::CANTIDAD_KEY . ' registrado en la BD para ' . $articulo['nombre'], 1);
                //Actualizar el stock
                $stock_cantidad[$clave_origen] -= $articulo['cantidad_salida'];
                $stock_cantidad->save();
            }

            if ($articulo['peso_salida'] > 0) {
                //Buscar el stock de peso
                $stock_cantidad = $stock->where('tipo', AlmacenConstants::PESO_KEY)->first();
                //Si no tiene stock de cantidad (unitario)
                if (!$stock_cantidad) throw new Exception("No hay stock " . AlmacenConstants::PESO_KEY . ' registrado en la BD para ' . $articulo['nombre'], 1);
                //Actualizar el stock
                $stock_cantidad[$clave_origen] -= $articulo['peso_salida'];
                $stock_cantidad->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.almacen.salidas.salidas-nueva');
    }
}

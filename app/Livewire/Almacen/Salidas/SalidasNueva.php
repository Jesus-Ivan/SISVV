<?php

namespace App\Livewire\Almacen\Salidas;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\DetallesSalida;
use App\Models\InventarioPrincipal;
use App\Models\Salida;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SalidasNueva extends Component
{
    public $codigo;
    public $cantidad;

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
        $info['detalleSalidas'] = $this->articulos;

        //Crear el detalle de la salida
        DB::transaction(function () use ($info) {
            $resultSalida = Salida::create([
                'destino' => $info['destino'],
                'observaciones' => $info['observaciones'],
                'fecha' => $this->fechaActual,
            ]);
            //REGISTRAMOS LOS DETALLES DE LA SALIDA EN LA TABLA CORRESPONDIENTE
            foreach ($info['detalleSalidas'] as $key => $articulo) {
                DetallesSalida::create([
                    'folio_salida' => $resultSalida->folio,
                    'codigo_articulo' => $articulo['codigo'],
                    'nombre' => $articulo['nombre'],
                    'cantidad' => $articulo['cantidad'],
                    'existencia_origen' => $articulo['stock']
                ]);
            }
            //RESTAMOS Y ACTUALIZAMOS EL STOCK DEL INVENTARIO PRINCIPAL
            foreach ($info['detalleSalidas'] as $key => $articulo) {
                //Buscar el articulo
                $result = InventarioPrincipal::find($articulo['codigo']);
                $result->update(
                    [
                        'stock' => $result->stock - $articulo['cantidad']
                    ]
                );
            }
        });
        //MENSAJE DE ALERTA
        session()->flash('success', "Salida registrada con exito");
        $this->dispatch('open-action-message');
        //RESETEAMOS LOS VALORES
        $this->reset(['destino', 'observaciones', 'articulos']);
    }

    public function render()
    {
        return view('livewire.almacen.salidas.salidas-nueva');
    }
}

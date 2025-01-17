<?php

namespace App\Livewire\Almacen\Traspasos;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\CatalogoVistaVerde;
use App\Models\DetallesTraspaso;
use App\Models\Stock;
use App\Models\Traspaso;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TraspasosNuevo extends Component
{
    //Fecha del dia de hoy
    public $today;
    //variables auxiliares para el modal
    public $cantidad, $peso;
    public $articulo_seleccionado, $origen_seleccionado, $destino_seleccionado;
    public $stock_origen_cantidad, $stock_origen_peso, $stock_destino_cantidad, $stock_destino_peso;

    public $observaciones, $lista_articulos = [];

    /**
     * Hook de inicio
     */
    public function mount()
    {
        //Establecemos la fecha actual
        $this->today = now()->toDateString();
    }

    /**
     * Hook se ejecuta cada vez que se actualiza una propiedad, desde la vista del componente.
     * No se ejecuta cuando un componente hijo en el interior un componente padre actualiza una propiedad mediante un evento
     */
    public function updated($property, $value)
    {
        if ($this->articulo_seleccionado) {
            switch ($property) {
                case 'origen_seleccionado':
                    $this->stock_origen_cantidad = $this->buscarStock($value, $this->articulo_seleccionado['codigo'], AlmacenConstants::CANTIDAD_KEY);
                    $this->stock_origen_peso = $this->buscarStock($value, $this->articulo_seleccionado['codigo'], AlmacenConstants::PESO_KEY);
                    break;

                case 'destino_seleccionado':
                    $this->stock_destino_cantidad = $this->buscarStock($value, $this->articulo_seleccionado['codigo'], AlmacenConstants::CANTIDAD_KEY);
                    $this->stock_destino_peso = $this->buscarStock($value, $this->articulo_seleccionado['codigo'], AlmacenConstants::PESO_KEY);
                    break;
            }
        }
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[On('selected-articulo')]
    public function onSelectedArticulo($codigo)
    {
        //Resetar los campos
        $this->reset('articulo_seleccionado', 'cantidad', 'peso');
        //Buscar el nuevo articulo
        $articulo = CatalogoVistaVerde::find($codigo);
        //Si se encontro el articulo
        if ($articulo) {
            //Guardarlo en la propiedad del componente
            $this->articulo_seleccionado = $articulo->toArray();
            //Agregar la propiedad de consultado
            $this->articulo_seleccionado['consultado']  = now()->toDateTimeString();
            //Buscar el stock en origen 
            $this->stock_origen_cantidad = $this->buscarStock($this->origen_seleccionado, $articulo->codigo, AlmacenConstants::CANTIDAD_KEY);
            $this->stock_origen_peso = $this->buscarStock($this->origen_seleccionado, $articulo->codigo, AlmacenConstants::PESO_KEY);
            //Buscar el stock en destino
            $this->stock_destino_cantidad = $this->buscarStock($this->destino_seleccionado, $articulo->codigo, AlmacenConstants::CANTIDAD_KEY);
            $this->stock_destino_peso = $this->buscarStock($this->destino_seleccionado, $articulo->codigo, AlmacenConstants::PESO_KEY);
        }
    }

    /**
     * Agrega un articulo a la tabla 
     */
    public function agregarArticulo()
    {
        $validated = $this->validate([
            'articulo_seleccionado' => 'required',
            'origen_seleccionado' => 'required',
            'destino_seleccionado' => 'required',
        ]);

        if (is_null($this->cantidad) && is_null($this->peso)) {
            session()->flash('error_input', 'Ingresa cantidad o peso');
            return;
        }

        //Si se selecciono un articulo
        if ($this->articulo_seleccionado) {
            //Agregarlo a la lista de articulos
            $this->lista_articulos[] = [
                'codigo' => $validated['articulo_seleccionado']['codigo'],
                'nombre' => $validated['articulo_seleccionado']['nombre'],
                'cantidad' => $this->cantidad,
                'peso' => $this->peso,
                'clave_bodega_origen' => $this->origen_seleccionado,
                'existencias_origen' => $this->convertStock($this->stock_origen_cantidad, $this->stock_origen_peso, $this->origen_seleccionado),
                'clave_bodega_destino' => $this->destino_seleccionado,
                'existencias_destino' => $this->convertStock($this->stock_destino_cantidad, $this->stock_destino_peso, $this->destino_seleccionado),
                'consultado' => $validated['articulo_seleccionado']['consultado'],
            ];
            //Resetar los campos
            $this->reset(
                'articulo_seleccionado',
                'cantidad',
                'peso',
                'stock_origen_cantidad',
                'stock_origen_peso',
                'stock_destino_cantidad',
                'stock_destino_peso'
            );
        }
    }

    public function closeModal()
    {
        $this->dispatch("close-modal", name: "articulos-modal");
    }

    /**
     * Finaliza el proceso de la entrada
     */
    public function aplicarTraspaso()
    {
        //Valida la informacion
        $validated = $this->validate(['lista_articulos' => 'min:1|required']);

        try {
            DB::transaction(function () use ($validated) {
                //Crear el registro del traspaso
                $result_traspaso = Traspaso::create([
                    'id_user' => auth()->user()->id,
                    'observaciones' => $this->observaciones,
                    'movimientos' => count($validated['lista_articulos'])
                ]);
                //Crear los detalles del trapaso
                foreach ($validated['lista_articulos'] as $key => $articulo) {
                    DetallesTraspaso::create([
                        'folio_traspaso' => $result_traspaso->folio,
                        'codigo_articulo' => $articulo['codigo'],
                        'nombre' => $articulo['nombre'],
                        'cantidad' => $articulo['cantidad'] ?: null,
                        'peso' => $articulo['peso'] ?: null,
                        'clave_bodega_origen' => $articulo['clave_bodega_origen'],
                        'existencia_origen' => json_encode($articulo['existencias_origen']),
                        'clave_bodega_destino' => $articulo['clave_bodega_destino'],
                        'existencia_destino' => json_encode($articulo['existencias_destino']),
                        'consultado' => $articulo['consultado']
                    ]);
                }
                //Actualizamos los stocks
                $this->actualizarStock($validated['lista_articulos']);
            }, 2);
            //Limpiar todo el componente
            $this->reset();
            //Mensaje de session
            session()->flash('success', 'Traspaso realizado con éxito');
        } catch (\Throwable $th) {
            //Mensaje de session
            session()->flash('fail', $th->getMessage());
        } finally {
            //Evento para abrir el action message
            $this->dispatch("open-action-message");
        }
    }

    /**
     * Elimina un articulo de la tabla
     */
    public function removerArticulo($indexArticulo)
    {
        unset($this->lista_articulos[$indexArticulo]);
    }

    /**
     * Busca el stock de un articulo, en la tabla "stocks".
     * Donde $clave_bodega corresponde a la PrimaryKey de la tabla "bodegas", que a su vez es nombre de una columna en la tabla "stocks"
     * Donde $tipo_stock es el tipo de stock declarado en AlmacenConstants, que coincide con los registros de la tabla "stocks"
     */
    private function buscarStock($clave_bodega, $codigo_articulo, $tipo_stock)
    {
        //Si no hay la clave de bodega como parametro
        if (! $clave_bodega)
            return null;

        //Buscar el stock del producto
        $result = Stock::where([
            ['codigo_catalogo', '=', $codigo_articulo],
            ['tipo', '=', $tipo_stock]
        ])
            ->select('id', $clave_bodega, 'codigo_catalogo', 'tipo')
            ->first();
        //Si no se encontro el stock, devolver null
        if ($result)
            return $result->toArray();
        else
            return null;
    }

    /**
     * Convierte los array de stock, en arrays sencillos
     */
    private function convertStock($stock_cantidad, $stock_peso, $clave_bodega)
    {
        //Variable auxiliar
        $stock = [];

        //Si el stock esta definido
        if ($stock_cantidad) {
            $stock[AlmacenConstants::CANTIDAD_KEY] = $stock_cantidad[$clave_bodega];
        }
        if ($stock_peso) {
            $stock[AlmacenConstants::PESO_KEY] = $stock_peso[$clave_bodega];
        }
        return $stock;
    }

    /**
     * Aumenta o disminuye los stocks de la lista
     */
    private function actualizarStock($lista_articulos)
    {
        foreach ($lista_articulos as $key => $articulo) {
            //Buscar los stocks del articulo
            $stock = Stock::where('codigo_catalogo', $articulo['codigo'])->get();
            //Si no hay stocks
            if (!count($stock)) throw new Exception("El articulo: " . $articulo['nombre'] . ", no cuenta con ningun registro de stock", 1);

            //Si la cantidad del articulo que se desea traspasar es mayor a cero
            if ($articulo['cantidad'] > 0) {
                //Buscar el stock unitario
                $stock_cantidad = $stock->where('tipo', AlmacenConstants::CANTIDAD_KEY)->first();
                //Si no tiene stock de cantidad (unitario)
                if (!$stock_cantidad) throw new Exception("No hay stock " . AlmacenConstants::CANTIDAD_KEY . ' registrado en la BD para ' . $articulo['nombre'], 1);
                //Actualizar el stock
                $stock_cantidad[$articulo['clave_bodega_origen']] -= $articulo['cantidad'];
                $stock_cantidad[$articulo['clave_bodega_destino']] += $articulo['cantidad'];
                $stock_cantidad->save();
            }

            if ($articulo['peso'] > 0) {
                //Buscar el stock de peso
                $stock_cantidad = $stock->where('tipo', AlmacenConstants::PESO_KEY)->first();
                //Si no tiene stock de cantidad (unitario)
                if (!$stock_cantidad) throw new Exception("No hay stock " . AlmacenConstants::PESO_KEY . ' registrado en la BD para ' . $articulo['nombre'], 1);
                //Actualizar el stock
                $stock_cantidad[$articulo['clave_bodega_origen']] -= $articulo['peso'];
                $stock_cantidad[$articulo['clave_bodega_destino']] += $articulo['peso'];
                $stock_cantidad->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.almacen.traspasos.traspasos-nuevo');
    }
}

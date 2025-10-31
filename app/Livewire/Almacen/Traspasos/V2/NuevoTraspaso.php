<?php

namespace App\Livewire\Almacen\Traspasos\V2;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\DetallesRequisicion;
use App\Models\DetalleTraspasoNew;
use App\Models\Insumo;
use App\Models\MovimientosAlmacen;
use App\Models\Presentacion;
use App\Models\TraspasoNew;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class NuevoTraspaso extends Component
{
    public $clave_origen = '', $clave_destino;
    public $tipo_traspaso = null;
    public $search_input = '';
    public $fecha, $hora, $observaciones;
    public $selectedItems = [];
    public $lista_articulos = [];
    public $folio_requisicion = null;

    //Propiedades del modal para las presentaciones de un insumo
    public $selectedInsumo = null;
    public $indexInsumo = null;
    public $insumXpresent = [];
    public $totalInsumoModal = 0;

    //Propiedad para evitar cambios en las bodegas
    #[Locked]
    public $locked_b_origen = false; //Bodega origen
    #[Locked]
    public $locked_b_destino = false; //Bodega destino
    //Propiedad para bloquear el folio de requisicion
    public $locked_folio = false;

    //hook que monitorea la actualizacion del componente
    public function updated($property, $value)
    {
        //Si se actualizo el campo de busqueda
        if ($property === 'search_input') {
            //Limpiar los productos seleccionados previamente
            $this->selectedItems = [];
        }

        //Evitamos que el usuario ingrese la misma bodega en origen y destino
        if ($property === 'clave_origen' || $property === 'clave_destino') {
            $this->bloquearFolio();
            if ($this->clave_origen === $this->clave_destino && !empty($this->clave_origen)) {
                //Restablecemos la ultima bodega actualizada
                $this->reset($property);
                //Mostramos el mensaje
                session()->flash('fail', 'La bodega de origen y destino no pueden ser la misma.');
                $this->dispatch('traspaso');
            }
        }
    }

    //Hook de iniciodel componente
    public function mount()
    {
        //Fecha inicial
        $this->fecha = now()->toDateString();
        //Hora inicial
        $this->hora = now()->toTimeString('minute');
    }

    public function bloquearFolio()
    {
        $bodega_origen = Bodega::find($this->clave_origen);
        $bodega_destino = Bodega::find($this->clave_destino);
        if (
            $bodega_origen?->naturaleza == AlmacenConstants::PRESENTACION_KEY
            && $bodega_destino?->naturaleza == AlmacenConstants::PRESENTACION_KEY
        ) {
            $this->locked_folio = true;
        } elseif (
            $bodega_origen?->naturaleza == AlmacenConstants::INSUMOS_KEY
            && $bodega_destino?->naturaleza == AlmacenConstants::PRESENTACION_KEY
        ) {
            $this->locked_folio = true;
        } else {
            $this->locked_folio = false;
        }
    }

    public function actualizarItems()
    {
        //Limpia la lista de articulos seleccionados en el modal
        $this->reset('selectedItems');
    }

    //Elimina un articulo de la lista
    public function eliminarArticulo($index)
    {
        unset($this->lista_articulos[$index]);
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function articulos()
    {
        if ($this->clave_origen == '') {
            return [];
        }

        $naturaleza_origen = Bodega::find($this->clave_origen)->naturaleza;

        if ($naturaleza_origen == AlmacenConstants::PRESENTACION_KEY) {
            $result = Presentacion::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%")
                ->where('estado', true);
        } elseif ($naturaleza_origen == AlmacenConstants::INSUMOS_KEY) {
            $result = Insumo::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%")
                ->where('inventariable', true);
        }

        return $result->get()->take(100);
    }

    public function finalizarSeleccion()
    {
        if ($this->clave_origen && $this->clave_destino) {
            $naturaleza_origen = Bodega::find($this->clave_origen)->naturaleza;
            $naturaleza_destino = Bodega::find($this->clave_destino)->naturaleza;
            //Crear instacia del servicio del inventario
            $inventario = new InventarioService();
            //Obtener fecha y hora actuales
            $hoy = now();

            //Filtramos los productos seleccionados
            $total_seleccionados = array_filter($this->selectedItems, function ($val) {
                return $val;
            });

            //Recorrer todo el array de seleccionados
            foreach ($total_seleccionados as $key => $value) {
                if (Bodega::find($this->clave_origen)->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                    //Se busca la presentacion en base a su clave.
                    $producto = Presentacion::find($key);
                    // Se busca el insumo relacionado para obtener la unidad
                    $insumo_relacionado = Insumo::with('unidad')->find($producto->clave_insumo_base);
                    // Se anexa el producto al array de la tabla
                    $this->lista_articulos[] = [
                        'clave' => $producto->clave,
                        'descripcion' => $producto->descripcion,
                        'existencia' => $inventario->existenciasInsumo($producto->clave_insumo_base, $hoy->toDateString(), $hoy->toTimeString(), $this->clave_origen)[0],
                        'cantidad' => 1,
                        'unidad' => $insumo_relacionado->unidad->descripcion,
                        'rendimiento' => $producto->rendimiento,
                        'cantidad_insumo' => $producto->rendimiento,
                        'clave_insumo_base' => $producto->clave_insumo_base
                    ];
                } else {
                    //Se busca el insumo del producto en base a su clave.
                    $producto = Insumo::with('unidad')->find($key);
                    // Se anexa el producto al array de la tabla
                    $this->lista_articulos[] = [
                        'clave' => $producto->clave,
                        'descripcion' => $producto->descripcion,
                        'existencia' => $inventario->existenciasInsumo($producto->clave, $hoy->toDateString(), $hoy->toTimeString(), $this->clave_origen)[0],
                        'cantidad' => 1,
                        'unidad' => $producto->unidad,
                        'rendimiento' => $producto->rendimiento,
                        'cantidad_insumo' => $producto->rendimiento,
                        'clave_insumo_base' => $producto->clave_insumo_base
                    ];
                }
            }

            // Define el tipo de traspaso concatenando la naturaleza
            $this->tipo_traspaso = "{$naturaleza_origen}_{$naturaleza_destino}";

            //Si hay al menos 1 seleccionado
            if (count($total_seleccionados)) {
                //Bloquear el cambio de bodega de origen y destino
                $this->locked_b_origen = true;
                $this->locked_b_destino = true;
            }

            //Limpiar articulos seleccionados
            $this->selectedItems = [];
            //Limpiar campo de busqueda
            $this->search_input = '';
            //Cerramos el modal
            $this->dispatch('close-modal', name: 'modal-articulos');
        }
    }

    //Buscamos articulos mediante una requisicion
    public function buscarRequisicion()
    {
        //Validar bodegas seleccionadas
        $validated = $this->validate([
            'folio_requisicion' => 'required',
            'clave_origen' => "required",
            'clave_destino' => "required",
        ], [
            'folio_requisicion.required' => 'Ingrese requisicion',
            'clave_origen.required' => "Seleccione rigen",
            'clave_destino.required' => "Seleccione destino",
        ]);

        //Buscar las bodegas
        $bodega_origen = Bodega::find($validated['clave_origen']);
        $bodega_destino = Bodega::find($validated['clave_destino']);

        // Define el tipo de traspaso concatenando la naturaleza
        $this->tipo_traspaso = "{$bodega_origen->naturaleza}_{$bodega_destino->naturaleza}";

        if ($bodega_origen->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            $this->traspasoPresentacion();
        } else {
            $this->traspasoInsumo();
        }
    }


    /**
     * Prepara la tabla para un traspaso de articulos desde un origen\
     * cuya naturaleza sea "Presentaciones"
     */
    public function traspasoPresentacion()
    {
        //Buscar los detalles de la requisicion
        $result = DetallesRequisicion::where('folio_requisicion', $this->folio_requisicion)->get();
        //Crear instacia del servicio del inventario
        $inventario = new InventarioService();
        //Obtener fecha y hora actuales
        $hoy = now();
        if (count($result)) {
            //Bloquear bodega de origen
            $this->locked_b_origen = true;
            $this->locked_b_destino = true;

            //Agregar todos los items (de la requi) a la tabla
            foreach ($result as $key => $value) {
                $producto = Presentacion::find($value->clave_presentacion);
                $insumo_relacionado = Insumo::with('unidad')->find($producto->clave_insumo_base);

                //Se anexa el producto al array de la tabla
                $this->lista_articulos[] = [
                    'clave' => $value->clave_presentacion,
                    'descripcion' => $producto->descripcion,
                    'existencia' => $inventario->existenciasInsumo($producto->clave_insumo_base, $hoy->toDateString(), $hoy->toTimeString(), $this->clave_origen)[0],
                    'cantidad' => $value->cantidad,
                    'clave_insumo_base' => $producto->clave_insumo_base,
                    'rendimiento' => $producto->rendimiento,
                    'unidad' => $insumo_relacionado->unidad->descripcion,
                    'cantidad_insumo' => $value->cantidad * $producto->rendimiento,
                ];
            }
        }
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    /**
     * Prepara la tabla para un traspaso de articulos desde un origen\
     * cuya naturaleza sea "Insumos"
     */
    public function traspasoInsumo()
    {
        //Buscar los detalles de la requisicion
        $detalles = DetallesRequisicion::with('presentacion.insumo.unidad')
            ->where('folio_requisicion', $this->folio_requisicion)->get();
        //Crear instacia del servicio del inventario
        $inventario = new InventarioService();
        //Obtener fecha y hora actuales
        $hoy = now();

        //Si hay detalles en la BD
        if (count($detalles)) {
            //Bloquear bodega de origen
            $this->locked_b_origen = true;
            $this->locked_b_destino = true;

            //Agregar todos los elementos a la tabla
            foreach ($detalles as $detalle) {
                //Se anexa el producto al array de la tabla
                $this->lista_articulos[] = [
                    'clave' => $detalle->presentacion->insumo->clave,
                    'descripcion' => $detalle->presentacion->insumo->descripcion,
                    'existencia' => $inventario->existenciasInsumo($detalle->presentacion->clave_insumo_base, $hoy->toDateString(), $hoy->toTimeString(), $this->clave_origen)[0],
                    'cantidad' => $detalle->cantidad * $detalle->presentacion->rendimiento,
                    'unidad' => $detalle->presentacion->insumo->unidad,
                    'rendimiento' => null,
                    'cantidad_insumo' => null,
                ];
            }
        }
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    public function aplicarTraspaso()
    {
        //Obtener el usuario autenticado actualmente
        $user = auth()->user();

        //Validamos datos adicionales
        $validated = $this->validate([
            'clave_origen' => 'required',
            'clave_destino' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'lista_articulos' => 'min:1|required'
        ]);

        //Concatenamos fecha y hora
        $validated['fecha_existencias'] = Carbon::parse($validated['fecha'])->setTimeFromTimeString($validated['hora']);

        //Iniciamos la transaccion
        try {
            DB::transaction(function () use ($user, $validated) {
                //Registro de traspaso
                $result = TraspasoNew::create([
                    'folio_requisicion' => $this->folio_requisicion,
                    'id_user' => $user->id,
                    'nombre' => $user->name,
                    'clave_origen' => $validated['clave_origen'],
                    'clave_destino' => $validated['clave_destino'],
                    'observaciones' => $this->observaciones,
                    'fecha_existencias' => $validated['fecha_existencias']
                ]);

                //Buscar la bodega, despues de crear el registro de la entrada
                $bodega = Bodega::find($result->clave_origen);
                $fecha = now();
                //Recorrer todos los articulos de la tabla
                foreach ($validated['lista_articulos'] as $key => $row) {
                    //Creamos los detalles del traspaso
                    $this->detalleTraspso($row, $bodega, $result);
                }

                // Obtenemos las bodegas de origen y destino para determinar el tipo de traspaso
                $bodega_origen = Bodega::where('clave', $validated['clave_origen'])->first();
                $bodega_destino = Bodega::where('clave', $validated['clave_destino'])->first();

                // Si la bodega de origen es de tipo 'PRESENTACION', manejamos la salida de presentaciones.
                if ($bodega_origen && $bodega_origen->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                    $this->salidaPresentaciones($result, $validated['fecha_existencias']);
                } else {
                    // Si no, asumimos que la bodega de origen es de tipo 'INSUMO' y manejamos la salida de insumos.
                    $this->salidaInsumos($result, $validated['fecha_existencias']);
                }

                // Si la bodega de destino es de tipo 'PRESENTACION', manejamos la entrada de presentaciones.
                if ($bodega_destino && $bodega_destino->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                    $this->entradaPresentaciones($result, $validated['fecha_existencias']);
                } else {
                    // Si no, asumimos que la bodega de destino es de tipo 'INSUMO' y manejamos la entrada de insumos.
                    $this->entradaInsumos($result, $validated['fecha_existencias']);
                }
            });
            $this->resetValores();
            session()->flash('success', 'Traspaso aplicado correctamente');
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('traspaso');
    }

    //Metodo para limpiar el estado del componente
    private function resetValores()
    {
        $this->reset(
            'clave_origen',
            'clave_destino',
            'search_input',
            'observaciones',
            'selectedItems',
            'lista_articulos',
            'locked_b_origen',
            'locked_b_destino',
            'tipo_traspaso',
            'folio_requisicion'
        );
        //Reiniciamos valores iniciales
        $this->mount();
    }

    /**
     * Evitamos que el usuario ingrese valores negativos o vacios en la cantidad
     */
    public function actualizarCantidad($index)
    {
        //Verificamos que cantidad no este vacia o negativa
        if (strlen($this->lista_articulos[$index]['cantidad']) == 0 || $this->lista_articulos[$index]['cantidad'] < 0)
            $this->lista_articulos[$index]['cantidad'] = 1;

        // Recalcula la cantidad de insumo para el artículo
        $this->lista_articulos[$index]['cantidad_insumo'] =
            $this->lista_articulos[$index]['cantidad'] * $this->lista_articulos[$index]['rendimiento'];
    }

    /**
     * Creamos los registros de la tabla traspasos dependiendo la combinacion de bodegas
     */
    private function detalleTraspso(array $row, Bodega $bodega, TraspasoNew $traspaso)
    {
        //Atributos comunes
        $data = [
            'folio_traspaso' => $traspaso->folio,
            'clave_presentacion' => null,
            'clave_insumo' => null,
            'descripcion' => $row['descripcion'],
            'cantidad' => $row['cantidad'],
            'rendimiento' => $row['rendimiento'],
            'cantidad_insumo' => $row['cantidad'] * $row['rendimiento']
        ];

        //Modificar la clave segun la naturaleza de la bodega de destino
        if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            $data['clave_presentacion'] = $row['clave'];
        } else {
            $data['clave_insumo'] = $row['clave'];
            $data['cantidad'] = null;
            $data['rendimiento'] = null;
            $data['cantidad_insumo'] = $row['cantidad'];
        }
        //Creamos el registro de los detalles de traspaso
        DetalleTraspasoNew::create($data);
    }

    /**
     * Modal para especificar las presentaciones al momento de hacer un traspaso de PV a ALMACEN
     */
    public function detallesPresentacion($index)
    {
        $this->indexInsumo = $index;
        //Obtenemos el insumo de la lista de articulos
        $insumo = $this->lista_articulos[$index];
        if (isset($insumo['detalle_presentaciones']) && is_array($insumo['detalle_presentaciones'])) {
            $this->insumXpresent = $insumo['detalle_presentaciones'];
        } else {
            //Buscamos la presentacion relacionada
            $presentacion = Presentacion::where('clave_insumo_base', $insumo['clave'])->get();

            $this->insumXpresent = $presentacion->map(function ($item) {
                return [
                    'clave' => $item->clave,
                    'descripcion' => $item->descripcion,
                    'rendimiento' => $item->rendimiento,
                    'cantidad' => 0, // Inicia la cantidad en 0
                    'cantidad_insumo' => 0, // Inicia la cantidad de insumo en 0
                ];
            })->toArray();
        }


        // Resetear el total de insumo para el modal
        $this->totalInsumoModal = collect($this->insumXpresent)->sum('cantidad_insumo');
        $this->selectedInsumo = $insumo;

        $this->dispatch('open-modal', name: 'modal-detalles');
    }

    /**
     * Evitamos que el usuario ingrese valores negativos o vacios en la cantidad.
     * Se utiliza especificamente en el modal de la tabla INSUM a PRESENT
     */
    public function actualizarCantidadModal($index)
    {
        // Obtener la cantidad del array
        $cantidad = $this->insumXpresent[$index]['cantidad'];

        // Si la cantidad es negativa o vacía, establecerla en 0
        if (strlen($cantidad) == 0 || $cantidad < 0) {
            $this->insumXpresent[$index]['cantidad'] = 0;
            $cantidad = 0;
        }

        // Recalcular la cantidad de insumo para la presentación específica
        $this->insumXpresent[$index]['cantidad_insumo'] =
            $cantidad * $this->insumXpresent[$index]['rendimiento'];

        // Recalcular el total de insumo para todas las presentaciones en el modal
        $this->totalInsumoModal = collect($this->insumXpresent)
            ->sum('cantidad_insumo');
    }

    // Método para guardar los cambios y cerrar el modal
    public function guardarPresentaciones()
    {
        // Verificamos si hay un índice de insumo guardado
        if (is_null($this->indexInsumo)) {
            return;
        }
        // Obtenemos la cantidad total de insumo del modal
        $total_insumo = $this->totalInsumoModal;
        //Actualizamos la lista de insumos con la cantidad total de presentaciones
        $this->lista_articulos[$this->indexInsumo]['cantidad'] = $total_insumo;
        $this->lista_articulos[$this->indexInsumo]['cantidad_insumo'] = $total_insumo;

        $this->lista_articulos[$this->indexInsumo]['detalle_presentaciones'] = $this->insumXpresent;

        $this->dispatch('close-modal', name: 'modal-detalles');
    }

    /**
     * Esta funcion registra los movimientos de entrada usando la bodega de destino como PRESTENTACIONES.
     * Si es entre ALMACENES (Presentacion a Presentacion) crea los movimientos de almacen para cad presentacion.
     * Si es de PV a Almacen (Insumo a Presentacion) Itera sobre los insumos y luego
     * sobre las presentaciones que los componen para registrar el movimiento de cada una.
     */
    private function entradaPresentaciones(TraspasoNew $traspaso, Carbon $fecha)
    {
        //Comporbamos si el traspaso es de ALMACEN a ALMACEN (PRESENTACION a PRESENTACION)
        if ($this->tipo_traspaso === 'PRESEN_PRESEN') {
            foreach ($this->lista_articulos as $key => $row) {
                // Creamos el movimiento para cada presentación que entra al almacén.
                MovimientosAlmacen::create([
                    'folio_traspaso' => $traspaso->folio,
                    'clave_concepto' => AlmacenConstants::ENT_TRASP_KEY,
                    'clave_insumo' => $row['clave_insumo_base'],
                    'clave_presentacion' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                    'clave_bodega' => $traspaso->clave_destino,
                    'cantidad_presentacion' => $row['cantidad'],
                    'rendimiento' => $row['rendimiento'],
                    'cantidad_insumo' => $row['cantidad'] * $row['rendimiento'],
                    'costo' => 0,
                    'iva' => 0,
                    'costo_con_impuesto' => 0,
                    'importe' => 0,
                    'fecha_existencias' => $fecha->toDateTimeString(),
                ]);
            }
        } else {
            //Este registro se utiliza de PV a ALMACEN
            foreach ($this->lista_articulos as $key => $row) {
                // Verificamos que existan presentaciones
                if (isset($row['detalle_presentaciones']) && is_array($row['detalle_presentaciones'])) {
                    foreach ($row['detalle_presentaciones'] as $presentacion) {
                        // Registramos solo las presentaciones con cantidad mayor a cero.
                        if ($presentacion['cantidad'] > 0) {
                            MovimientosAlmacen::create([
                                'folio_traspaso' => $traspaso->folio,
                                'clave_concepto' => AlmacenConstants::ENT_TRASP_KEY,
                                'clave_insumo' => $row['clave'],
                                'clave_presentacion' => $presentacion['clave'],
                                'descripcion' => $presentacion['descripcion'],
                                'clave_bodega' => $traspaso->clave_destino,
                                'cantidad_presentacion' => $presentacion['cantidad'],
                                'rendimiento' => $presentacion['rendimiento'],
                                'cantidad_insumo' => $presentacion['cantidad'] * $presentacion['rendimiento'],
                                'costo' => 0,
                                'iva' => 0,
                                'costo_con_impuesto' => 0,
                                'importe' => 0,
                                'fecha_existencias' => $fecha->toDateTimeString(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Registra la salida de articulos de la bodega de origen PRESENTACIONES, multiplicandola por -1 para indicar que es
     * una salida de articulo.
     */
    private function salidaPresentaciones(TraspasoNew $traspaso, Carbon $fecha)
    {
        foreach ($this->lista_articulos as $key => $row) {
            //Movimientos de almacen
            MovimientosAlmacen::create([
                'folio_traspaso' => $traspaso->folio,
                'clave_concepto' => AlmacenConstants::SAL_TRASP_KEY,
                'clave_insumo' => $row['clave_insumo_base'],
                'clave_presentacion' => $row['clave'],
                'descripcion' => $row['descripcion'],
                'clave_bodega' => $traspaso->clave_origen,
                'cantidad_presentacion' => $row['cantidad'] * -1,
                'rendimiento' => $row['rendimiento'],
                'cantidad_insumo' => ($row['cantidad'] * $row['rendimiento']) * -1,
                'costo' => 0,
                'iva' => 0,
                'costo_con_impuesto' => 0,
                'importe' => 0,
                'fecha_existencias' => $fecha->toDateTimeString(),
            ]);
        }
    }

    /**
     * Esta funcion registra los movimientos de entrada usando la bodega de destino como INSUMOS.
     * Si es de ALMACEN a PV (PRESENTACION a INSUMO) convierte la cantidad de presentaciones a insumos para el registro.
     * Si es de PV a PV (INSUMO a INSUMO) utiliza la cantidad de insumos directamente.
     */
    private function entradaInsumos(TraspasoNew $traspaso, Carbon $fecha)
    {
        foreach ($this->lista_articulos as $key => $row) {
            $clave_insumo = null;
            $cantidad_insumo = 0;

            // Determinamos la clave y cantidad del insumo según el tipo de traspaso.
            if ($this->tipo_traspaso === 'PRESEN_INSUM') {
                //Si el traspaso es de ALMACEN a PV (PRESENTACION a INSUMO)
                $clave_insumo = $row['clave_insumo_base'];
                $cantidad_insumo = $row['cantidad'] * $row['rendimiento'];
            } else {
                //Si el traspaso es de PV a PV (INSUMO a INSUMO)
                $clave_insumo = $row['clave'];
                $cantidad_insumo = $row['cantidad'];
            }

            // Buscamos la descripción del insumo para el registro.
            $insumo = Insumo::find($clave_insumo);
            $descripcionInsumo = $insumo ? $insumo->descripcion : 'NOMBRE NO ENCONTRADO';

            // Creamos el movimiento de entrada en la bodega.
            MovimientosAlmacen::create([
                'folio_traspaso' => $traspaso->folio,
                'clave_concepto' => AlmacenConstants::ENT_TRASP_KEY,
                'clave_insumo' => $clave_insumo,
                'descripcion' => $descripcionInsumo,
                'clave_bodega' => $traspaso->clave_destino,
                'cantidad_insumo' => $cantidad_insumo,
                'costo' => 0,
                'iva' => 0,
                'costo_con_impuesto' => 0,
                'importe' => 0,
                'fecha_existencias' => $fecha->toDateTimeString(),
            ]);
        }
    }

    /**
     * Esta funcion registra los movimientos de salida usando la bodega de origen como INSUMOS, multiplicandola por 
     * -1 para indicar que es una salida de articulo.
     */
    private function salidaInsumos(TraspasoNew $traspaso, Carbon $fecha) //clave origen
    {
        foreach ($this->lista_articulos as $key => $row) {
            //Movimientos de almacen
            MovimientosAlmacen::create([
                'folio_traspaso' => $traspaso->folio,
                'clave_concepto' => AlmacenConstants::SAL_TRASP_KEY,
                'clave_insumo' => $row['clave'],
                'descripcion' => $row['descripcion'],
                'clave_bodega' => $traspaso->clave_origen,
                'cantidad_insumo' => $row['cantidad'] * -1,
                'costo' => 0,
                'iva' => 0,
                'costo_con_impuesto' => 0,
                'importe' => 0,
                'fecha_existencias' => $fecha->toDateTimeString(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.almacen.traspasos.v2.nuevo-traspaso');
    }
}

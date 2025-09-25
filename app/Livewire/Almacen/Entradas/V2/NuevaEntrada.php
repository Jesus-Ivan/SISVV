<?php

namespace App\Livewire\Almacen\Entradas\V2;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\DetalleEntradaNew;
use App\Models\DetallesRequisicion;
use App\Models\EntradaNew;
use App\Models\Insumo;
use App\Models\MovimientosAlmacen;
use App\Models\Presentacion;
use App\Models\Proveedor;
use App\Models\Requisicion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class NuevaEntrada extends Component
{
    public $clave_bodega = '', $search_input = '', $folio_requi = null;
    public $fecha, $hora, $observaciones;
    public $selectedItems = [], $articulos_table = [];
    #[Locked]
    public $locked_bodega = false;  //Propiedad para evitar el cambio de bodega

    //Hook de inicio del vida del componente
    public function mount()
    {
        //Establecer fecha inicial
        $this->fecha = now()->toDateString();
        //Establecer hora inicial
        $this->hora = now()->toTimeString("minute");
    }

    //hook que monitorea la actualizacion del componente
    public function updated($property, $value)
    {
        //Si se actualizo el campo de busqueda
        if ($property === 'search_input') {
            //Limpiar los productos seleccionados previamente
            $this->selectedItems = [];
        }
    }

    public function actualizarProveedor($eValue)
    {
        //Recorrer todo el array de la tabla de articulos
        foreach ($this->articulos_table as $key => $item) {
            //Actualizar el proveedor de cada item
            $this->articulos_table[$key]['id_proveedor'] = $eValue;
        }
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function articulos()
    {
        if ($this->clave_bodega == '') {
            return [];
        } elseif (Bodega::find($this->clave_bodega)->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            $result = Presentacion::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%")
                ->where('estado', true);
        } elseif (Bodega::find($this->clave_bodega)->naturaleza == AlmacenConstants::INSUMOS_KEY) {
            $result = Insumo::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%")
                ->where('inventariable', true);
        }

        return  $result->get()->take(100);
    }
    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    public function agregarArticulos()
    {
        //Filtramos los productos seleccionados, cuyo valor sea true del checkBox
        $total_seleccionados = array_filter($this->selectedItems, function ($val) {
            return $val;
        });
        //Instancia de la clase para el servicio de inventario
        $invService = new InventarioService();

        //Recorrer todo el array de seleccionados
        foreach ($total_seleccionados as $key => $value) {
            if (Bodega::find($this->clave_bodega)->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                //Se busca la presentacion en base a su clave.
                $producto = Presentacion::find($key);
            } else {
                //Se busca el insumo del producto en base a su clave.
                $producto = Insumo::with('unidad')->find($key);
            }
            //Calcular calcular el importe_sin_impuesto
            $importe_sin_impuesto = $invService->obtenerImporte($producto['costo'], 1);
            //Calcular importe con impuesto
            $importe = $invService->obtenerImporte($producto['costo_con_impuesto'], 1);

            //Se anexa el producto al array de la tabla
            $this->articulos_table[] = [
                'clave' => $producto->clave,
                'descripcion' => $producto->descripcion,
                'cantidad' => 1,
                'costo' => $producto->costo,
                'iva' => $producto->iva,
                'costo_con_impuesto' => $producto->costo_con_impuesto,
                'clave_insumo_base' => $producto->clave_insumo_base,
                'rendimiento' => $producto->rendimiento,
                'id_proveedor' => $producto->id_proveedor,
                'importe_sin_impuesto' => $importe_sin_impuesto,
                'impuesto' => $importe - $importe_sin_impuesto,
                'importe' => $importe,
                'unidad' => $producto->unidad,
            ];
        }

        //Si hay al menos 1 seleccionado
        if (count($total_seleccionados)) {
            //Bloquear el cambio de bodega
            $this->locked_bodega = true;
        }

        //Limpiar articulos seleccionados
        $this->selectedItems = [];
        //Limpiar campo de busqueda
        $this->search_input = '';
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    public function buscarRequisicion()
    {
        $validated = $this->validate([
            'clave_bodega' => "required"
        ], [
            'clave_bodega.required' => "Seleccione"
        ]);

        //Buscar la bodega (seleccionada) en la BD
        $bodega = Bodega::find($validated['clave_bodega']);

        //Segun la naturaleza de la bodega
        if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            $this->requisicionPresentaciones();
        } else {
            $this->requisicionInsumos();
        }
    }

    /**
     * Busca los elementos de una requisicion y los prepara\
     * para el trapaso a una bodega con naturaleza "PRESEN"
     */
    public function requisicionPresentaciones()
    {
        //Buscar los detalles de la requisicion
        $result = DetallesRequisicion::where('folio_requisicion', $this->folio_requi)->get();
        //Si hay al menos 1 registro correspondiente
        if (count($result)) {
            //Bloquear la bodega
            $this->locked_bodega = true;

            //Agregar todos los items (de la requi) a la tabla
            foreach ($result as $key => $value) {
                $producto = Presentacion::find($value->clave_presentacion);
                //Se anexa el producto al array de la tabla
                $this->articulos_table[] = [
                    'clave' => $value->clave_presentacion,
                    'descripcion' => $producto->descripcion,
                    'cantidad' => $value->cantidad,
                    'costo' => $value->costo_unitario,
                    'iva' => $value->iva,
                    'costo_con_impuesto' => $value->costo_con_impuesto,
                    'clave_insumo_base' => $producto->clave_insumo_base,
                    'rendimiento' => $producto->rendimiento,
                    'id_proveedor' => $value->id_proveedor,
                    'importe' => $value->importe, //Es lo mismo que multiplicar cantidad * costo_con_impuesto
                    'unidad' => $producto->unidad,
                ];
            }
        }
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    /**
     * Busca los elementos de una requisicion y los prepara\
     * para el trapaso a una bodega con naturaleza "INSUM"
     */
    public function requisicionInsumos()
    {
        //Buscar los detalles de la requisicion
        $detalle_requi = DetallesRequisicion::with('presentacion')
            ->where('folio_requisicion', $this->folio_requi)->get();
        //Si hay al menos 1 registro correspondiente
        if (count($detalle_requi)) {
            //Bloquear la bodega
            $this->locked_bodega = true;

            //Agregar todos los items (de la requi) a la tabla
            foreach ($detalle_requi as $key => $detalle) {
                //Buscar el insumo base
                $insumo = Insumo::with('unidad')
                    ->find($detalle->presentacion->clave_insumo_base);
                $cant_insum = $detalle->cantidad * $detalle->presentacion->rendimiento;
                $costo_unitario = round($detalle->costo_unitario / $detalle->presentacion->rendimiento, 3);
                $costo_con_impuesto = round($detalle->costo_con_impuesto / $detalle->presentacion->rendimiento, 3);
                //Se anexa el producto al array de la tabla
                $this->articulos_table[] = [
                    'clave' => $insumo->clave,
                    'descripcion' => $insumo->descripcion,
                    'cantidad' => $cant_insum,
                    'costo' => $costo_unitario,
                    'iva' => $detalle->iva,
                    'costo_con_impuesto' => $costo_con_impuesto,
                    'clave_insumo_base' => $detalle->presentacion->clave_insumo_base,
                    'id_proveedor' => $detalle->id_proveedor,
                    'importe' => $cant_insum * $costo_con_impuesto,
                    'unidad' => $insumo->unidad,
                ];
            }
        }
        //Emitimos evento para cerrar el componente del modal
        $this->dispatch('close-modal');
    }

    public function actualizarItems()
    {
        //Limpia la lista de articulos seleccionados en el modal
        $this->reset('selectedItems');
    }

    /**
     * Esta funcion elimina el elemento del array de la tabla, segun su indice
     */
    public function eliminarArticulo($index)
    {
        unset($this->articulos_table[$index]);
    }

    public function aplicarEntrada()
    {
        //Mutiplicar la tabla (por si existio error de livewire)
        foreach ($this->articulos_table as $i => $value) {
            $this->updateCostoSinIva($i);
        }

        //Obtener el usuario autenticado actualmente
        $user = auth()->user();

        //Validar parametros iniciales
        $validated = $this->validate([
            'clave_bodega' => 'required|min:1',
            'fecha' => 'required|date',
            'hora' => 'required',
            'articulos_table' => 'min:1'
        ]);
        //Concatenar fecha y hora (y agregarlo al array validado)
        $validated['fecha_existencias'] = Carbon::parse($validated['fecha'])->setTimeFromTimeString($validated['hora']);

        //Calcular iva
        $iva = round(array_sum(array_column($this->articulos_table, 'impuesto')), 2);
        //calcular subtotal
        $subtotal = round(array_sum(array_column($this->articulos_table, 'importe_sin_impuesto')), 2);

        //Iniciar transaccion
        try {
            DB::transaction(function () use ($user, $validated, $subtotal, $iva) {
                //Crear registro de la entrada
                $result = EntradaNew::create([
                    'folio_requisicion' => $this->folio_requi,
                    'clave_bodega' => $validated['clave_bodega'],
                    'fecha_existencias' => $validated['fecha_existencias'],
                    'observaciones' => $this->observaciones,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $subtotal + $iva,
                    'id_user' => $user->id,
                    'nombre' => $user->name,
                ]);
                //Buscar la bodega, despues de crear el registro de la entrada
                $bodega = Bodega::find($result->clave_bodega);
                //Recorrer todos los articulos de la tabla
                foreach ($validated['articulos_table'] as $key => $row) {
                    //Crear detalles de la entrada
                    $this->createDetalleEntrada($row, $bodega, $result);
                    //Crear movimientos de inventario
                    $this->createMovimientoAlmacen($row, $bodega, $result);
                }
            }, 2);
            session()->flash('success-entrada', 'Entrada registrada correctamente');   //Mensaje de sesion
            $this->reset('clave_bodega', 'search_input', 'folio_requi', 'observaciones', 'selectedItems', 'articulos_table', 'locked_bodega');     //Limpiar propiedades
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());   //Mensaje de sesion de error
        }
        $this->dispatch('entrada');  //Emitimos evento para mostrar el message-alert
    }

    /**
     * Realiza el registro del movimiento en la tabla 'movimientos_almacen' segun la bodega dada
     */
    public function createMovimientoAlmacen(array $row, Bodega $bodega, EntradaNew $entrada)
    {
        if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            //Movimientos de almacen (presentaciones)
            MovimientosAlmacen::create([
                'folio_entrada' => $entrada->folio,
                'clave_concepto' => AlmacenConstants::ENT_KEY,
                'clave_insumo' => $row['clave_insumo_base'],
                'clave_presentacion' => $row['clave'],
                'descripcion' => $row['descripcion'],
                'clave_bodega' => $entrada->clave_bodega,
                'cantidad_presentacion' => $row['cantidad'],
                'rendimiento' => $row['rendimiento'],
                'cantidad_insumo' => $row['cantidad'] * $row['rendimiento'],
                'costo' => $row['costo'],
                'iva' => $row['iva'],
                'costo_con_impuesto' => $row['costo_con_impuesto'],
                'importe' => $row['importe'],
                'fecha_existencias' => $entrada->fecha_existencias,
            ]);
        } elseif ($bodega->naturaleza == AlmacenConstants::INSUMOS_KEY) {
            //Movimientos de almacen (insumos)
            MovimientosAlmacen::create([
                'folio_entrada' => $entrada->folio,
                'clave_concepto' => AlmacenConstants::ENT_KEY,
                'clave_insumo' => $row['clave'],
                'descripcion' => $row['descripcion'],
                'clave_bodega' => $entrada->clave_bodega,
                'cantidad_insumo' => $row['cantidad'],
                'costo' => $row['costo'],
                'iva' => $row['iva'],
                'costo_con_impuesto' => $row['costo_con_impuesto'],
                'importe' => $row['importe'],
                'fecha_existencias' => $entrada->fecha_existencias,
            ]);
        } else {
            //Lanzar excepcion
            throw new Exception("La bodega: " . $bodega->descripcion . ", no tiene naturaleza definida", 1);
        }
    }

    /**
     * Crea el registro en la tabla 'entradas_new', segun el tipo
     */
    public function createDetalleEntrada(array $row, Bodega $bodega, EntradaNew $entrada)
    {
        //Si el proveedor es null o un string vacio
        if (is_null($row['id_proveedor']) || $row['id_proveedor'] == "")
            throw new Exception("Falta proveedor: " . $row['descripcion'], 1);

        //Crear array con atributos comunes
        $data = [
            'folio_entrada' => $entrada->folio,
            'clave_presentacion' => null,
            'clave_insumo' => null,
            'descripcion' => $row['descripcion'],
            'id_proveedor' => $row['id_proveedor'],
            'cantidad' => $row['cantidad'],
            'costo_unitario' => $row['costo'],
            'iva' => $row['iva'],
            'costo_con_impuesto' => $row['costo_con_impuesto'],
            'importe_sin_impuesto' => $row['importe_sin_impuesto'],
            'impuesto' => $row['impuesto'],
            'importe' => $row['importe'],
        ];

        //Modificar la clave segun la naturaleza de la bodega de destino
        if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY)
            $data['clave_presentacion'] = $row['clave'];
        else
            $data['clave_insumo'] = $row['clave'];
        //Crear el registro del detalle de la entrada
        DetalleEntradaNew::create($data);
    }

    //Se ejecuta al actualizar el iva desde el front
    public function updateCostoIva($index)
    {
        $this->actualizarCostoIva($index);
        $this->actualizarImporte($index);
    }

    /**
     * Se ejcuta al actualizar el costo con inva desde el front. (o al momento de guardar la entrada)
     */
    public function updateCostoSinIva($index)
    {
        $this->actualizarCostoSinIva($index);
        $this->actualizarImporte($index);
    }

    /**
     * Mutiplica la cantidad de un insumo/presentacion por su costo con impuesto
     */
    public function actualizarImporte($index)
    {
        //Verificar que cantidad no sea vacio
        if (strlen($this->articulos_table[$index]['cantidad']) == 0)
            $this->articulos_table[$index]['cantidad'] = 1;
        //Actualizar la cantidad, por su absoluto
        $this->articulos_table[$index]['cantidad'] = abs($this->articulos_table[$index]['cantidad']);

        //Variable auxiliar
        $row = $this->articulos_table[$index];
        //Instacia de clase del inventario
        $invService = new InventarioService();

        //Calcular calcular el importe_sin_impuesto
        $importe_sin_impuesto = $invService->obtenerImporte($row['costo'], $row['cantidad']);
        //Calcular importe con impuesto
        $importe = $invService->obtenerImporte($row['costo_con_impuesto'], $row['cantidad']);
        //Actualizar valores
        $this->articulos_table[$index]['impuesto'] = $importe - $importe_sin_impuesto;
        $this->articulos_table[$index]['importe_sin_impuesto'] = $importe_sin_impuesto;
        $this->articulos_table[$index]['importe'] = $importe;
    }

    /**
     * Calcula el precio con iva de la tabla 'presentaciones'
     */
    public function actualizarCostoIva($index)
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->articulos_table[$index]['iva']) == 0)
            $this->articulos_table[$index]['iva'] = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->articulos_table[$index]['costo']) == 0)
            $this->articulos_table[$index]['costo'] = '0';

        $costo_unitario = $this->articulos_table[$index]['costo']; //variables axuliares
        $iva = $this->articulos_table[$index]['iva']; //variables axuliares

        //Calcular costo con iva
        $costo_iva = $costo_unitario + ($costo_unitario * ($iva / 100));
        $this->articulos_table[$index]['costo_con_impuesto'] = round($costo_iva, 2);
    }

    /**
     * Calcula el costo unitario sin iva de la tabla 'presentaciones', segun el indice dado
     */
    public function actualizarCostoSinIva($index)
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->articulos_table[$index]['costo_con_impuesto']) == 0)
            $this->articulos_table[$index]['costo_con_impuesto'] = '0';

        //Variables auximilares
        $costo_con_impuesto = $this->articulos_table[$index]['costo_con_impuesto'];
        $iva = $this->articulos_table[$index]['iva'];

        //Calcular Costo sin iva
        $costo_sin_iva = ($costo_con_impuesto * 100) / (100 + $iva);
        $this->articulos_table[$index]['costo'] = round($costo_sin_iva, 2);
    }


    public function render()
    {
        return view('livewire.almacen.entradas.v2.nueva-entrada');
    }
}

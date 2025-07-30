<?php

namespace App\Livewire\Almacen\Entradas\V2;

use App\Constants\AlmacenConstants;
use App\Models\Bodega;
use App\Models\DetalleEntradaNew;
use App\Models\EntradaNew;
use App\Models\Insumo;
use App\Models\MovimientosAlmacen;
use App\Models\Presentacion;
use App\Models\Proveedor;
use Carbon\Carbon;
use DragonCode\Contracts\Cashier\Auth\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NuevaEntrada extends Component
{
    public $clave_bodega = '', $search_input = '', $folio_requi;
    public $fecha, $hora, $observaciones;
    public $selectedItems = [], $articulos_table = [];

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
            $result = Presentacion::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%");
        } elseif (Bodega::find($this->clave_bodega)->naturaleza == AlmacenConstants::INSUMOS_KEY) {
            $result = Insumo::whereAny(['descripcion', 'clave'], 'like', "%$this->search_input%");
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

        //Recorrer todo el array de seleccionados
        foreach ($total_seleccionados as $key => $value) {
            if (Bodega::find($this->clave_bodega)->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                //Se busca la presentacion en base a su clave.
                $producto = Presentacion::find($key);
            } else {
                //Se busca el insumo del producto en base a su clave.
                $producto = Insumo::with('unidad')->find($key);
            }

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
                'importe' => $producto->costo_con_impuesto, //Es lo mismo que multiplicar cantidad * costo_con_impuesto
                'unidad' => $producto->unidad,
            ];
        }

        //Limpiar articulos seleccionados
        $this->selectedItems = [];
        //Limpiar campo de busqueda
        $this->search_input = '';
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
        $iva = $this->calcularIva($this->articulos_table);

        //calcular subtotal
        $subtotal = $this->calcularSubtotal($this->articulos_table);

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
                    'total' => round($subtotal + $iva, 2),
                    'id_user' => $user->id,
                    'nombre' => $user->name,
                ]);
                //Recorrer todos los articulos de la tabla
                foreach ($validated['articulos_table'] as $key => $row) {
                    //Crear detalles de la entrada
                    $this->createDetalleEntrada($row, Bodega::find($result->clave_bodega), $result);
                    //Crear movimientos de inventario
                    $this->createMovimientoAlmacen($row, Bodega::find($result->clave_bodega), $result);
                }
            }, 2);
            session()->flash('success-entrada', 'Entrada registrada correctamente');   //Mensaje de sesion
            $this->reset();     //Limpiar propiedades
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());   //Mensaje de sesion de error
        }
        $this->dispatch('entrada');  //Emitimos evento para mostrar el message-alert
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * cantidad)
     */
    public function calcularSubtotal($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Mutiplicar y acumular el valor
            $acu += $presentacion['costo'] * $presentacion['cantidad'];
        }
        return $acu;
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * (iva / 100)) * $cantidad'
     */
    public function calcularIva($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Mutiplicar y acumular el valor
            $acu += ($presentacion['costo'] * ($presentacion['iva'] / 100)) * $presentacion['cantidad'];
        }
        return $acu;
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
                'clave_concepto' => '',
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
        } else {
            //Movimientos de almacen (insumos)
            MovimientosAlmacen::create([
                'folio_entrada' => $entrada->folio,
                'clave_concepto' => '',
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
        }
    }

    /**
     * Crea el registro en la tabla 'entradas_new', segun el t
     */
    public function createDetalleEntrada(array $row, Bodega $bodega, EntradaNew $entrada)
    {
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


    public function render()
    {
        return view('livewire.almacen.entradas.v2.nueva-entrada');
    }
}

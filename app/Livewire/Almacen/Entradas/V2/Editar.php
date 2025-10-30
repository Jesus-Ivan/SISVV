<?php

namespace App\Livewire\Almacen\Entradas\V2;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\DetalleEntradaNew;
use App\Models\EntradaNew;
use App\Models\Insumo;
use App\Models\Presentacion;
use App\Models\Proveedor;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editar extends Component
{
    #[Locked]
    public $clave_bodega = '';
    #[Locked]
    public $fecha, $hora;
    public $observaciones;
    public $articulos_table = [];
    public $cuenta = "", $proveedor = "";
    #[Locked]
    public $folio;


    //Hook de inicio de vida del componente.
    public function mount($folio)
    {
        $entrada = EntradaNew::with('detalles')->find($folio);
        //Guardar propiedades en el componente
        $this->observaciones = $entrada->observaciones;
        $fecha_existencias = Carbon::parse($entrada->fecha_existencias);
        $this->fecha = $fecha_existencias->toDateString();
        $this->hora = $fecha_existencias->toTimeString();
        $this->clave_bodega = $entrada->clave_bodega;
        $this->folio = $folio;

        foreach ($entrada->detalles as $key => $detalle) {
            //Se anexa el producto al array de la tabla
            $this->articulos_table[] = [
                'id' => $detalle->id,
                'clave' => $detalle->clave_presentacion ?: $detalle->clave_insumo,
                'clave_presentacion' => $detalle->clave_presentacion,
                'clave_insumo' => $detalle->clave_insumo,
                'descripcion' => $detalle->descripcion,
                'cuenta' => $detalle->cuenta_contable,
                'cantidad' =>  $detalle->cantidad,
                'costo' => $detalle->costo_unitario,
                'iva' => $detalle->iva,
                'costo_con_impuesto' => $detalle->costo_con_impuesto,
                'id_proveedor' => $detalle->id_proveedor,
                'factura' => $detalle->factura,
                'cuenta_contable' => $detalle->cuenta_contable,
                'importe_sin_impuesto' => $detalle->importe_sin_impuesto,
                'impuesto' => $detalle->impuesto,
                'importe' => $detalle->importe,
                'unidad' => $detalle->insumo->unidad,
            ];
        }
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    //Hook que monitorea el estado del componente
    public function updated($property, $val)
    {
        if ($property == "cuenta") {
            for ($i = 0; $i < count($this->articulos_table); $i++) {
                $this->articulos_table[$i]['cuenta_contable'] = $val;
            }
        }
        if ($property == "proveedor") {
            for ($i = 0; $i < count($this->articulos_table); $i++) {
                $this->articulos_table[$i]['id_proveedor'] = $val;
            }
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


    //Actualiza los valores de la entrada (excepto la cantidad)
    public function actualizarEntrada()
    {
        //Mutiplicar la tabla (por si existio error de livewire)
        foreach ($this->articulos_table as $i => $value) {
            $this->updateCostoSinIva($i);
        }

        try {
            //Validar que todos los registros tengan proveedor
            $this->validarProveedores();
            //Iniciar transaccion
            DB::transaction(function () {
                $this->updateEntrada();
                $this->updateDetalles();
                $this->updatePrecios();
            });
            //Redirigir al usuario en caso de exito
            $this->redirectRoute('almacen.entradav2');
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
            //Evento de apertura del action message
            $this->dispatch("entrada");
        }
    }


    /**
     * Valida si cada insumo/presentacion tiene proveedor asignado
     */
    private function validarProveedores()
    {
        foreach ($this->articulos_table as $key => $row) {
            if (!$row['id_proveedor'])
                throw new Exception("Falta proveedor: " . $row['descripcion']);
        }
    }

    /**
     * Actualiza los valores de la entrada (tabla: 'entradas_new')
     */
    private function updateEntrada()
    {
        //Calcular iva
        $iva = round(array_sum(array_column($this->articulos_table, 'impuesto')), 2);
        //calcular subtotal
        $subtotal = round(array_sum(array_column($this->articulos_table, 'importe_sin_impuesto')), 2);

        EntradaNew::where('folio', $this->folio)
            ->update([
                'observaciones' => $this->observaciones,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $subtotal + $iva,
            ]);
    }

    /**
     * Actualiza los detalles de la entrada. (tabla: 'detalles_entradas')
     */
    private function updateDetalles()
    {
        foreach ($this->articulos_table as $row) {
            DetalleEntradaNew::where('id', $row['id'])
                ->update([
                    'id_proveedor' => $row['id_proveedor'],
                    'factura' => $row['factura'] ?: null,
                    'cuenta_contable' => $row['cuenta_contable'],
                    'costo_unitario' => $row['costo'],
                    'iva' => $row['iva'],
                    'costo_con_impuesto' => $row['costo_con_impuesto'],
                    'importe_sin_impuesto' => $row['importe_sin_impuesto'],
                    'impuesto' => $row['impuesto'],
                    'importe' => $row['importe'],
                ]);
        }
    }

    /**
     * Actualiza los costos del insumo y de las presentaciones.\
     * Segun los articulos registrados (presentaciones o insumos) en el movimiento.
     */
    public function updatePrecios()
    {
        //Sumar la columa de "clave_presentacion"
        $suma_clave_presentacion = array_sum(array_column($this->articulos_table, 'clave_presentacion'));
        //Sumar la columa de "clave_insumo"
        $suma_clave_insumo = array_sum(array_column($this->articulos_table, 'clave_insumo'));

        //Crear instacia del servicio
        $service = new InventarioService();
        //Para cada fila de los detalles de la entrada
        foreach ($this->articulos_table as $key => $row) {
            //
            if ($suma_clave_presentacion) {
                //Actualizar el costo de la presentacion
                $service->actualizarCostoPresen($row, $this->fecha);
            } elseif ($suma_clave_insumo) {
                //Actualizar costo del insumo
                $service->actualizarCostoInsum($row, $this->fecha);
            }
        }
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
        return view('livewire.almacen.entradas.v2.editar', [
            'cuentas' => AlmacenConstants::METODOS_PAGO
        ]);
    }
}

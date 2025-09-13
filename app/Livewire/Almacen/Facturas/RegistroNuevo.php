<?php

namespace App\Livewire\Almacen\Facturas;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\DetallesFacturas;
use App\Models\Facturas;
use App\Models\Insumo;
use App\Models\Presentacion;
use App\Models\Proveedor;
use App\Models\Requisicion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RegistroNuevo extends Component
{
    public $id_proveedor;
    public $cuenta_contable;
    public $fecha_compra;
    public $fecha_vencimiento; // Opcional
    public $folio_entrada;     // Opcional
    public $folio_remision;    // Opcional
    public $observaciones;

    public $searchPresentacion = '';
    public $selectedGrupo = null;
    public $selectedPresentacion = [];
    public $listaPresentaciones = [];

    public $folio_search;   // Opcional, para buscar un folio de requisicion

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

    #[Computed()]
    public function presentaciones()
    {
        //Se inicia la consulta para obtener las presentaciones
        $query = DB::table('presentaciones')
            ->whereNot('estado', 0);

        //Se filtra por el grupo seleccionado
        if ($this->selectedGrupo) {
            $query->where('id_grupo', $this->selectedGrupo);
        }

        if (!empty($this->searchPresentacion)) {
            $query->where(function ($q) {
                $q->where('descripcion', 'like', '%' . $this->searchPresentacion . '%')
                    ->orWhere('clave', 'like', '%' . $this->searchPresentacion . '%');
            });
        }

        return $query->get();
    }

    //Cuando cambia el grupo seleccionado en el SELECT resetea la lista de presentaciones
    public function updateSelect()
    {
        $this->reset('searchPresentacion', 'selectedPresentacion');
    }

    public function finalizarSeleccion()
    {
        $total_seleccionados = array_filter($this->selectedPresentacion, function ($val) {
            return $val;
        });

        if (count($total_seleccionados) > 0) {
            $this->dispatch('selectedPresentaciones', $total_seleccionados);
            $this->dispatch('close-modal');
            $this->reset(['searchPresentacion', 'selectedGrupo', 'selectedPresentacion']);
        }
    }

    #[On('selectedPresentaciones')]
    public function onFinishSelect(array $total_seleccionados)
    {
        foreach ($total_seleccionados as $key => $value) {
            $result = Presentacion::find($key);
            if ($result) {
                array_push($this->listaPresentaciones, [
                    'temp' => time(),
                    'clave' => $key,
                    'descripcion' => $result->descripcion,
                    'cantidad' => 1,
                    'costo_unitario' => $result->costo,
                    'iva' => $result->iva,
                    'impuesto' => $result->impuesto,
                    'costo_con_impuesto' => $result->costo_con_impuesto,
                    'importe_sin_impuesto' => $result->importe_sin_impuesto,
                    'importe' => $result['costo_con_impuesto']
                ]);
            }
        }
        //Multiplicar toda la tabla, para calcular el costo sin iva.
        foreach ($this->listaPresentaciones as $i => $presentacion) {
            $this->costoSinIvaActualizado($i);
        }
    }

    /**
     * Metodo para buscar requisiciones.
     * Puede ser opcional crear una factura mediante una requisicion
     */
    public function buscarRequisicion()
    {
        //Validamos el folio de la requisicion
        $this->validate([
            'folio_search' => 'required|numeric',
        ]);

        // Buscamos la requisicion 
        $requisicion = Requisicion::with('detalles.presentacion')
            ->find($this->folio_search);

        // Limpiamos la lista actual
        $this->reset('listaPresentaciones');

        //Asignamos el folio de la requisición
        $this->folio_entrada = $requisicion->folio;

        foreach ($requisicion->detalles as $detalle) {
            // Verificamos que los datos de la presentacion existan en la base de datos
            if ($detalle && $detalle->presentacion) {
                $presentacion = $detalle->presentacion;

                // Agregamos el resultado a la lista
                array_push($this->listaPresentaciones, [
                    'temp' => time() . $presentacion->id,
                    'clave' => $presentacion->clave,
                    'descripcion' => $presentacion->descripcion,
                    'cantidad' => $detalle->cantidad, // Usamos la cantidad de la requisición
                    'costo_unitario' => $detalle->costo_unitario,
                    'iva' => $detalle->iva,
                    'impuesto' => $detalle->impuesto,
                    'costo_con_impuesto' => $detalle->costo_con_impuesto,
                    'importe_sin_impuesto' => $detalle->importe_sin_impuesto,
                    'importe' => $detalle->importe
                ]);
            }
        }
    }

    // Método para eliminar una presentación de la lista temporal
    public function removePresentacion($index)
    {
        unset($this->listaPresentaciones[$index]);
    }

    //Actualiza el importe total en la tabla
    public function importeActualizado($index)
    {
        $this->actualizarImporte($index);
    }

    //Actualiza el costo en la tabla
    public function costoIvaActualizado($index)
    {
        $this->actualizaCostoIva($index);
        $this->actualizarImporte($index);
    }

    //Actualiza el costo sin iva en la tabla
    public function costoSinIvaActualizado($index)
    {
        $this->actualizaCostoSinIva($index);
        $this->actualizarImporte($index);
    }

    //Creamos la factura y los detalles que le corresponden
    public function crearFactura()
    {
        try {
            $validated = $this->validate([
                'id_proveedor' => 'required',
                'cuenta_contable' => 'required',
                'fecha_compra' => 'required|date',
                'fecha_vencimiento' => 'nullable|date',
                'folio_entrada' => 'nullable',
                'folio_remision' => 'nullable|string|max:255',
                'observaciones' => 'nullable|string',
                'listaPresentaciones' => 'required|array|min:1'
            ]);

            //Mutiplicar toda la tabla (calcular el costo sin iva y el importe)
            foreach ($this->listaPresentaciones as $i => $presentacion) {
                $this->costoSinIvaActualizado($i);
            }

            //Iniciamos la transaccion
            DB::transaction(function () use ($validated) {

                //Calculamos los valores necesaios
                $subtotal = array_sum(array_column($validated['listaPresentaciones'], 'importe_sin_impuesto'));
                $iva = array_sum(array_column($validated['listaPresentaciones'], 'impuesto'));

                //Creamos el registro para la tabla 'Facturas'
                $result_factura = Facturas::create([
                    'fecha_compra' => $validated['fecha_compra'],
                    'fecha_vencimiento' => $validated['fecha_vencimiento'] ?: NULL,
                    'folio_entrada' => $validated['folio_entrada'] ?: NULL,
                    'id_proveedor' => $validated['id_proveedor'],
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $subtotal + $iva,
                    'cuenta_contable' => $validated['cuenta_contable'],
                    'folio_remision' => $validated['folio_remision'],
                    'user_name' => auth()->user()->name,
                    'observaciones' => $validated['observaciones']
                ]);

                foreach ($validated['listaPresentaciones'] as $key => $presentacion) {
                    //Creamos los detalles de la factura
                    DetallesFacturas::create([
                        'folio_compra' => $result_factura->folio,
                        'clave_presentacion' => $presentacion['clave'],
                        'cantidad' => $presentacion['cantidad'],
                        'costo' => $presentacion['costo_unitario'],
                        'iva' => $presentacion['iva'],
                        'impuesto' => $presentacion['impuesto'],
                        'costo_con_impuesto' => $presentacion['costo_con_impuesto'],
                        'importe_sin_impuesto' => $presentacion['importe_sin_impuesto'],
                        'importe' => $presentacion['importe']
                    ]);

                    //Actualizamos dos datos en la tabla 'Presentaciones'
                    $presentacionUpdate = Presentacion::where('clave', $presentacion['clave'])->first();
                    if ($presentacionUpdate) {
                        $nuevaFecha = $validated['fecha_compra'];
                        $fechaExistente = $presentacionUpdate->ultima_compra;
                        $costo_rend = round($presentacion['costo_unitario'] / $presentacionUpdate->rendimiento, 2);
                        $costo_rend_impuesto = round($presentacion['costo_con_impuesto'] / $presentacionUpdate->rendimiento, 2);

                        // Si la fecha de compra es mayor a la fecha existente o no cuenta con fecha, actualizamos la fecha
                        if (is_null($fechaExistente) || $nuevaFecha >= $fechaExistente) {
                            $presentacionUpdate->update([
                                'costo' => $presentacion['costo_unitario'],
                                'iva' => $presentacion['iva'],
                                'costo_con_impuesto' => $presentacion['costo_con_impuesto'],
                                'ultima_compra' => $nuevaFecha,
                                'costo_rend' => $costo_rend,
                                'costo_rend_impuesto' => $costo_rend_impuesto,
                            ]);

                            //Actualizamos el insumo en la tabla 'Insumos', para ello obtenemos el insumo relacionado a la presentación
                            $insumoUpdate = Insumo::where('clave', $presentacionUpdate->clave_insumo_base)->first();

                            if ($insumoUpdate) {
                                $insumoFechaExistente = $insumoUpdate->ultima_compra;

                                // Si la nueva fecha de compra es mayor que la del insumo, o no existe, actualizamos el insumo
                                if (is_null($insumoFechaExistente) || $nuevaFecha >= $insumoFechaExistente) {
                                    //Encontramos el costo_con_impuesto más alto de las presentaciones
                                    $presentacion = Presentacion::where('clave_insumo_base', $insumoUpdate->clave)
                                        ->orderByDesc('costo_con_impuesto')
                                        ->first();

                                    $insumoUpdate->update([
                                        'costo' => $presentacion->costo_rend,
                                        'iva' => $presentacion->iva,
                                        'costo_con_impuesto' => $presentacion->costo_rend_impuesto,
                                        'ultima_compra' => $nuevaFecha
                                    ]);
                                }
                            }
                        }
                    }
                }
            });

            $this->reset();
            session()->flash('success', 'Factura registrada exitosamente.');
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    /**
     * Multiplica la cantidad de la presentacion * el costo con impuesto
     */
    private function actualizarImporte($index)
    {
        //Verificar que cantidad no sea vacio
        if (strlen($this->listaPresentaciones[$index]['cantidad']) == 0 || $this->listaPresentaciones[$index]['cantidad'] < 1)
            $this->listaPresentaciones[$index]['cantidad'] = 1;
        //Variable auxiliar
        $row = $this->listaPresentaciones[$index];
        //Instacia de clase del inventario
        $invService = new InventarioService();
        //Calcular el importe sin impuesto
        $importe_sin_impuesto = $invService->obtenerImporte($row['costo_unitario'], $row['cantidad']);
        //Calcular el importe con impuesto
        $importe = $invService->obtenerImporte($row['costo_con_impuesto'], $row['cantidad']);

        //Actualizar valores
        $this->listaPresentaciones[$index]['impuesto'] = $importe - $importe_sin_impuesto;
        $this->listaPresentaciones[$index]['importe_sin_impuesto'] = $importe_sin_impuesto;
        $this->listaPresentaciones[$index]['importe'] = $importe;
    }

    /**
     * Calcula el precio con iva
     */
    private function actualizaCostoIva($index)
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->listaPresentaciones[$index]['iva']) == 0 || $this->listaPresentaciones[$index]['iva'] < 1)
            $this->listaPresentaciones[$index]['iva'] = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->listaPresentaciones[$index]['costo_unitario']) == 0 || $this->listaPresentaciones[$index]['costo_unitario'] < 1)
            $this->listaPresentaciones[$index]['costo_unitario'] = '0';

        //Variables auxiliares
        $costo = $this->listaPresentaciones[$index]['costo_unitario'];
        $iva = $this->listaPresentaciones[$index]['iva'];

        //Calcular costo con iva
        $costo_iva = $costo + ($costo * ($iva / 100));
        $this->listaPresentaciones[$index]['costo_con_impuesto'] = round($costo_iva, 2);
    }

    /**
     * Calcula el costo unitario sin iva
     */
    private function actualizaCostoSinIva($index)
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->listaPresentaciones[$index]['costo_con_impuesto']) == 0 || $this->listaPresentaciones[$index]['costo_con_impuesto'] < 1)
            $this->listaPresentaciones[$index]['costo_con_impuesto'] = '0';

        //Variables auximilares
        $costo_con_impuesto = $this->listaPresentaciones[$index]['costo_con_impuesto'];
        $iva = $this->listaPresentaciones[$index]['iva'];

        //Calcular Costo sin iva
        $costo_sin_iva = ($costo_con_impuesto * 100) / (100 + $iva);
        $this->listaPresentaciones[$index]['costo_unitario'] = round($costo_sin_iva, 2);
    }

    public function render()
    {
        return view(
            'livewire.almacen.facturas.registro-nuevo',
            [
                'metodo_pago' => AlmacenConstants::METODOS_PAGO
            ]
        );
    }
}

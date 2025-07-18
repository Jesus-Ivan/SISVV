<?php

namespace App\Livewire\Almacen\Inventario;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\ConceptoAlmacen;
use App\Models\DetallesInventario;
use App\Models\Grupos;
use App\Models\Insumo;
use App\Models\Inventario;
use App\Models\MovimientosAlmacen;
use App\Models\Presentacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InventarioNuevo extends Component
{
    //Propiedades del modal
    public $clave_bodega = '', $fecha_inv, $hora_inv, $seleccionar_general = false;
    public $lista_grupos = [];

    //Propiedades del componente
    public $table = [], $total_inv_teorico = 0, $total_inv_real = 0, $total_diferencia = 0, $observaciones;

    //Propiedad que determina el estado del modal.
    #[Locked]
    public $loaded = false;

    //Hook de inicio del vida del componente
    public function mount()
    {
        //Establecer fecha inicial
        $this->fecha_inv = now()->toDateString();
        //Establecer hora inicial
        $this->hora_inv = now()->toTimeString("minute");
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
    }

    #[Computed()]
    public function grupos()
    {
        $result = Grupos::where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        return $result;
    }

    #[Computed()]
    public function conceptos()
    {
        return ConceptoAlmacen::where('visible_inv_fisico', 1)
            ->orderBy('tipo')
            ->get();
    }

    public function agregar()
    {
        $validated = $this->validate([
            'clave_bodega' => 'required',
            'fecha_inv' => 'required',
            'hora_inv' => 'required'
        ]);

        //Obtenemos la bodega seleccionada, apartir de su clave.
        $bodega = $this->bodegas->find($validated['clave_bodega']);

        //Instanciar objeto para consultar las existencias
        $service = new InventarioService();

        //Para cada grupo, en la lista de grupos seleccionados
        foreach ($this->lista_grupos as $id => $status) {
            //Si status es true
            if ($status) {
                //Si la naturaleza de la bodega seleccionada es de 'presentaciones'
                if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                    //Fusionar el array de resultados provisto de la consulta
                    $this->table = array_merge(
                        $this->table,
                        $service->consultarPresentaciones(Grupos::find($id), $validated['fecha_inv'], $validated['hora_inv'], $validated['clave_bodega'])
                    );
                    $this->actualizarTotalesPresentacion();
                } else {
                    //Fusionar el array de resultados provisto de la consulta
                    $this->table = array_merge(
                        $this->table,
                        $service->consultarInsumos(Grupos::find($id), $validated['fecha_inv'], $validated['hora_inv'], $validated['clave_bodega'])
                    );
                    $this->actualizarTotalesInsumo();
                }
            }
        }
        //Cambiar el estado
        $this->loaded = true;
        //Emitir evento para cerrar modal
        $this->dispatch('close-modal');
    }

    public function seleccionar()
    {
        //Limpiar lista
        $this->reset('lista_grupos');
        //Si esta seleccionada la casilla general de grupos
        if ($this->seleccionar_general) {
            //Agregar el grupos a la lista
            foreach ($this->grupos as $grupo) {
                //Marcar las casillas
                $this->lista_grupos[$grupo->id] = true;
            }
        }
    }

    public function guardar()
    {
        //Concatenar fecha y hora
        $fecha = Carbon::parse($this->fecha_inv)->setTimeFromTimeString($this->hora_inv);
        //Obtenemos la bodega seleccionada, apartir de su clave.
        $bodega = $this->bodegas->find($this->clave_bodega);

        try {
            //Si la naturaleza de la bodega seleccionada es de 'presentaciones'
            if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
                $this->guardarInvPresentacion($fecha);
            } else {
                $this->guardarInvInsumo($fecha);
            }
            //Mensaje de sesion
            session()->flash('success', 'Ajuste realizado con exito !!');
            //Limpiar componente
            $this->reset();
        } catch (\Throwable $th) {
            //Mensaje de sesion
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento del alert
        $this->dispatch('open-action-message');
    }

    /**
     * Guarda el inventario fisico. de acuerdo al tipo de bodega seleccionada (Presentaciones)
     */
    private function guardarInvPresentacion(Carbon $fecha)
    {
        //Mutiplicamos toda la tabla, si hubo algun error de livewire
        foreach ($this->table as $i => $value) {
            $this->actualizarReal($i, false);
        }

        DB::transaction(function () use ($fecha) {
            //Crear el registro del inventario fisico
            $inv_fisico = Inventario::create([
                'clave_bodega' => $this->clave_bodega,
                'id_user' => auth()->user()->id,
                'nombre' => auth()->user()->name,
                'fecha_existencias' => $fecha->toDateTimeString(),
                'observaciones' => $this->observaciones,
            ]);
            //Crear detalles del inventario fisico (Solo almacen)
            foreach ($this->table as $key => $row) {
                //Si la diferencia es 0 (no hay ajuste de inventario)
                if ($row['diferencia'] == 0)
                    continue;   //Omitir la iteracion
                //Detalle de inventario
                DetallesInventario::create([
                    'folio_inventario' => $inv_fisico->folio,
                    'clave_presentacion' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                    'stock_teorico' => $row['existencias_presentacion'],
                    'stock_fisico' =>  $row['existencias_real'],
                    'diferencia_almacen' =>  $row['diferencia'],
                    'diferencia_importe' =>  $row['diferencia_importe'],
                ]);
                //Movimientos de almacen
                MovimientosAlmacen::create([
                    'folio_inventario' => $inv_fisico->folio,
                    'clave_concepto' => $row['clave_concepto'],
                    'clave_insumo' => $row['clave_insumo_base'],
                    'clave_presentacion' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                    'clave_bodega' => $inv_fisico->clave_bodega,
                    'cantidad_presentacion' => $row['diferencia'],
                    'rendimiento' => $row['rendimiento'],
                    'cantidad_insumo' => $row['diferencia'] * $row['rendimiento'],
                    'costo' => $row['costo'],
                    'iva' => $row['iva'],
                    'costo_con_impuesto' => $row['costo_con_impuesto'],
                    'importe' => $row['diferencia_importe'],
                    'fecha_existencias' => $fecha->toDateTimeString(),
                ]);
            }
        }, 2);
    }

    /**
     * Guarda el inventario fisico. de acuerdo al tipo de bodega seleccionada (Insumos)
     */
    private function guardarInvInsumo(Carbon $fecha)
    {
        //Mutiplicamos toda la tabla, si hubo algun error de livewire
        foreach ($this->table as $i => $value) {
            $this->actualizarReal($i, false);
        }
        DB::transaction(function () use ($fecha) {
            //Crear el registro del inventario fisico
            $inv_fisico = Inventario::create([
                'clave_bodega' => $this->clave_bodega,
                'id_user' => auth()->user()->id,
                'nombre' => auth()->user()->name,
                'fecha_existencias' => $fecha->toDateTimeString(),
                'observaciones' => $this->observaciones,
            ]);
            //Crear detalles del inventario fisico (Solo almacen)
            foreach ($this->table as $key => $row) {
                //Si la diferencia es 0 (no hay ajuste de inventario)
                if ($row['diferencia'] == 0)
                    continue;   //Omitir la iteracion
                //Detalle de inventario
                DetallesInventario::create([
                    'folio_inventario' => $inv_fisico->folio,
                    'clave_insumo' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                    'stock_teorico' => $row['existencias_insumo'],
                    'stock_fisico' =>  $row['existencias_real'],
                    'diferencia_almacen' =>  $row['diferencia'],
                    'diferencia_importe' =>  $row['diferencia_importe'],
                ]);
                //Movimientos de almacen
                MovimientosAlmacen::create([
                    'folio_inventario' => $inv_fisico->folio,
                    'clave_concepto' => $row['clave_concepto'],
                    'clave_insumo' => $row['clave'],
                    'descripcion' => $row['descripcion'],
                    'clave_bodega' => $inv_fisico->clave_bodega,
                    'cantidad_insumo' => $row['diferencia'],
                    'costo' => $row['costo'],
                    'iva' => $row['iva'],
                    'costo_con_impuesto' => $row['costo_con_impuesto'],
                    'importe' => $row['diferencia_importe'],
                    'fecha_existencias' => $fecha->toDateTimeString(),
                ]);
            }
        }, 2);
    }

    /**
     * Calcula la diferencia de la presentacion teorica y fisica. Segun un indice dado\
     * Utilizado para la tabla del componente.
     */
    public function actualizarReal($index, bool $update_concepto)
    {
        //Obtenemos la bodega seleccionada, apartir de su clave.
        $bodega = $this->bodegas->find($this->clave_bodega);
        //Verificamos el tipo de bodega
        if ($bodega->naturaleza == AlmacenConstants::PRESENTACION_KEY) {
            //Calculamos y actualizamos la diferencias
            $this->diferenciaPresentacion($index, $update_concepto);
        } else {
            //Calculamos y actualizamos la diferencias
            $this->diferenciaInsumo($index, $update_concepto);
        }

        //Si la diferencia es diferente de cero y (no) se deba actualizar el concepto
        if ($this->table[$index]['diferencia'] != 0 && !$update_concepto) {
            //Si el concepto es una string vacia (antes de guardar en la BD)
            if (! strlen($this->table[$index]['clave_concepto']))
                throw new Exception('Falta concepto en: ' . $this->table[$index]['descripcion']);
        }
    }

    /**
     * Calcula las diferencias de la tabla, segun el indice dado (presentaciones)
     */
    private function diferenciaPresentacion($index, bool $update_concepto = true)
    {
        $row = &$this->table[$index];
        //Si la existencia es un campo vacio
        if (strlen($row['existencias_real']) == 0)
            $row['existencias_real'] =  $row['existencias_presentacion'];                //Reestablecer valor

        $dif = $row['existencias_real'] - $row['existencias_presentacion'];     //Calcular diferencia en las existencias de la presentacion
        //Actualizar los atributos
        $row['diferencia'] = $dif;
        $row['diferencia_importe'] = $dif * $row['costo_con_impuesto'];

        //Actualizar el concepto del ajuste, segun el valor de la diferencia
        if ($update_concepto) {
            if ($dif > 0)
                $row['clave_concepto'] = AlmacenConstants::ENT_AJUSTE_KEY;
            elseif ($dif == 0)
                $row['clave_concepto'] = '';
            else
                $row['clave_concepto'] = AlmacenConstants::SAL_AJUSTE_KEY;
        }

        // Actualizar las diferencias totales
        $this->actualizarTotalesPresentacion();
    }

    /**
     * Calcula las diferencias de la tabla, segun el indice dado (insumos)
     */
    private function diferenciaInsumo($index, bool $update_concepto = true)
    {
        $row = &$this->table[$index];
        //Si la existencia es un campo vacio
        if (strlen($row['existencias_real']) == 0)
            $row['existencias_real'] =  $row['existencias_insumo'];                //Reestablecer valor

        $dif = $row['existencias_real'] - $row['existencias_insumo'];     //Calcular diferencia en las existencias de la presentacion
        //Actualizar los atributos
        $row['diferencia'] = $dif;
        $row['diferencia_importe'] = $dif * $row['costo_con_impuesto'];
        //Actualizar el concepto del ajuste
        if ($update_concepto) {
            if ($dif > 0)
                $row['clave_concepto'] = AlmacenConstants::ENT_AJUSTE_KEY;
            elseif ($dif == 0)
                $row['clave_concepto'] = '';
            else
                $row['clave_concepto'] = AlmacenConstants::SAL_AJUSTE_KEY;
        }
        // Actualizar las diferencias totales
        $this->actualizarTotalesInsumo();
    }

    /**
     * Calcula el total del inventario (teorico, fisico y la diferencia) para las presentaciones
     */
    private function actualizarTotalesPresentacion()
    {
        //Calcular total teorico de las presentaciones
        $this->total_inv_teorico = $this->calcularTotal('costo_con_impuesto', 'existencias_presentacion');
        $this->total_inv_real = $this->calcularTotal('costo_con_impuesto', 'existencias_real');
        $this->total_diferencia = round($this->total_inv_real - $this->total_inv_teorico, 2);
    }

    /**
     * Calcula el total del inventario (teorico, fisico y la diferencia) para los insumos
     */
    private function actualizarTotalesInsumo()
    {
        //Calcular total teorico de las presentaciones
        $this->total_inv_teorico = $this->calcularTotal('costo_con_impuesto', 'existencias_insumo');
        $this->total_inv_real = $this->calcularTotal('costo_con_impuesto', 'existencias_real');
        $this->total_diferencia = round($this->total_inv_real - $this->total_inv_teorico, 2);
    }

    /**
     * Obtiene y devuelve la sumatoria (column_costo * colum_cantidad) de la tabla del componente.
     */
    private function calcularTotal($column_costo, $colum_cantidad)
    {
        $total = 0;
        foreach ($this->table as $key => $row) {
            $total += $row[$column_costo] * $row[$colum_cantidad];
        }
        return round($total, 2);
    }

    public function render()
    {
        return view('livewire.almacen.inventario.inventario-nuevo', [
            'presentaciones' => AlmacenConstants::PRESENTACION_KEY,
        ]);
    }
}

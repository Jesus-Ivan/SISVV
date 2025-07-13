<?php

namespace App\Livewire\Almacen\Facturas;

use App\Constants\AlmacenConstants;
use App\Models\DetallesFacturas;
use App\Models\Facturas;
use App\Models\Presentacion;
use App\Models\Proveedor;
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
                    'costo' => $result->costo,
                    'iva' => $result->iva,
                    'costo_con_impuesto' => $result->costo_con_impuesto,
                    'importe' => $result['costo_con_impuesto']
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

            $subtotal = $this->calcularSubtotal($validated['listaPresentaciones']);
            $iva = $this->calcularIva($validated['listaPresentaciones']);

            //Creamos el registro para la tabla 'Facturas'
            $result_factura = Facturas::create([
                'fecha_compra' => $validated['fecha_compra'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'folio_entrada' => $validated['folio_entrada'],
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
                    'costo' => $presentacion['costo'],
                    'iva' => $presentacion['iva'],
                    'impuesto' => $presentacion['costo_con_impuesto'],
                    'importe' => $presentacion['importe']
                ]);
            }
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
        if (strlen($this->listaPresentaciones[$index]['cantidad']) == 0)
            $this->listaPresentaciones[$index]['cantidad'] = 1;
        //Calcula el importe
        $this->listaPresentaciones[$index]['importe'] =
            $this->listaPresentaciones[$index]['cantidad'] * $this->listaPresentaciones[$index]['costo_con_impuesto'];
    }

    /**
     * Calcula el precio con iva
     */
    private function actualizaCostoIva($index)
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->listaPresentaciones[$index]['iva']) == 0)
            $this->listaPresentaciones[$index]['iva'] = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->listaPresentaciones[$index]['costo']) == 0)
            $this->listaPresentaciones[$index]['costo'] = '0';

        //Variables auxiliares
        $costo = $this->listaPresentaciones[$index]['costo'];
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
        if (strlen($this->listaPresentaciones[$index]['costo_con_impuesto']) == 0)
            $this->listaPresentaciones[$index]['costo_con_impuesto'] = '0';

        //Variables auximilares
        $costo_con_impuesto = $this->listaPresentaciones[$index]['costo_con_impuesto'];
        $iva = $this->listaPresentaciones[$index]['iva'];

        //Calcular Costo sin iva
        $costo_sin_iva = ($costo_con_impuesto * 100) / (100 + $iva);
        $this->listaPresentaciones[$index]['costo'] = round($costo_sin_iva, 2);
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * cantidad)
     */
    private function calcularSubtotal($listaPresentaciones)
    {
        $acu = 0;
        foreach ($listaPresentaciones as $presentacion) {
            //Mutiplicar y acumular el valor
            $acu += $presentacion['costo'] * $presentacion['cantidad'];
        }
        return $acu;
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * (iva / 100)) * $cantidad'
     */
    private function calcularIva($listaPresentaciones)
    {
        $acu = 0;
        foreach ($listaPresentaciones as $presentacion) {
            //Mutiplicar y acumular el valor
            $acu += ($presentacion['costo'] * ($presentacion['iva'] / 100)) * $presentacion['cantidad'];
        }
        return $acu;
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

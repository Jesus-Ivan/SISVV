<?php

namespace App\Livewire\Almacen\Produccion;

use App\Constants\AlmacenConstants;
use App\Libraries\InventarioService;
use App\Livewire\Forms\ProduccionesForm;
use App\Models\Bodega;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevaProduccion extends Component
{
    public ProduccionesForm $form;

    public $fecha_existencias, $hora_existencias, $observaciones;

    //Insumos elaborados, seleccionados en el MODAL de agregar
    public $insumos_elaborados = [], $search_insumo_elaborado = '';
    public $clave_origen, $clave_destino, $locked_bodegas = false;


    //Insumo seleccionado para modificar receta (modo edicion)
    #[Locked]
    public $insumo_editable = null, $index_editable = null;
    //Insumos requeridos para elaborar un insumo (modo edicion)
    public $insumos_receta = [];


    #[Computed()]
    public function insumos()
    {
        return Insumo::with('unidad')
            ->whereAny(['clave', 'descripcion'], 'like', '%' . $this->search_insumo_elaborado . '%')
            ->where('elaborado', 1)
            ->orderBy('descripcion')
            ->limit(100)->get();
    }

    #[Computed()]
    public function bodegas()
    {
        return Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)
            ->where('naturaleza', AlmacenConstants::INSUMOS_KEY)
            ->get();
    }

    public function mount()
    {
        $hoy = now();
        //Establecer fecha y hora inicial
        $this->fecha_existencias = $hoy->toDateString();
        $this->hora_existencias = $hoy->toTimeString('minute');
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        //Buscar el insumo nuevo
        $insum = Insumo::with('unidad')->find($clave)->toArray();
        //Buscar la existencia del insumo nuevo
        $service = new  InventarioService();
        $result = $service->existenciasInsumo(
            $clave,
            $this->fecha_existencias,
            $this->hora_existencias,
            $this->clave_origen
        );
        //Filtrar insumos seleccionados
        $selected_insum = array_filter($this->insumos_receta, function ($insum) {
            return $insum['selected'];
        });
        //Si hay al menos 1 insumo seleccionado de la receta
        if (count($selected_insum)) {
            //Reemplazar los valores en el array 'insumos_receta'. (por el nuevo insumo)
            foreach ($selected_insum as $key => $row) {
                $this->insumos_receta[$key]['clave_insumo'] = $clave;
                $this->insumos_receta[$key]['ingrediente'] = $insum;
                $this->insumos_receta[$key]['existencias_origen'] = reset($result)['existencias_insumo'];
            }
        } else {
            //Agregar el nuevo insumo a la receta
            array_push($this->insumos_receta, [
                'clave_insumo_elaborado' => $this->insumo_editable['clave'],
                'clave_insumo' => $clave,
                'cantidad' => null,
                'cantidad_c_merma' => null,
                'total' => 0,
                'ingrediente' => $insum,
                'selected' => false,
                'existencias_origen' => reset($result)['existencias_insumo']
            ]);
        }
        //Limpiar la seleccion de los insumos
        $this->insumos_receta = array_map(function ($insum) {
            $insum['selected'] = false;
            return $insum;
        }, $this->insumos_receta);
    }

    public function finalizarSeleccion()
    {
        //Validar bodega de origen y destino
        $validated = $this->validate([
            'clave_origen' => 'required',
            'clave_destino' => 'required',
            'fecha_existencias' => 'required',
            'hora_existencias' => 'required',
        ], [
            'clave_origen.required' => 'Requerido',
            'clave_destino.required' => 'Requerido',
            'fecha_existencias.required' => 'Requerido',
            'hora_existencias.required' => 'Requerido',
        ]);
        //Array de condiciones (para las existencias)
        $condiciones = [
            ['fecha_existencias', '<=', $validated['fecha_existencias'] . ' ' . $validated['hora_existencias']],
            ['clave_bodega', '=', $validated['clave_destino']]
        ];
        //Recorrer los Insumos elaborados, seleccionados del modal
        foreach ($this->insumos_elaborados as $key => $val) {
            //Si esta seleccionado (true)
            if ($val) {
                //Obtenemos el insumo que coincidan con la clave y sus existencias
                $result = Insumo::query()
                    ->with(['unidad', 'receta.ingrediente.unidad'])
                    ->withSum([
                        'movimientosAlmacen' => fn($query) => $query->where($condiciones)
                    ], 'cantidad_insumo')
                    ->where('clave', $key)
                    ->first();
                //Agregar los items al array del form
                $this->form->agregarInsumo($result);
                //Bloquear las bodegas
                $this->locked_bodegas = true;
            }
        }
        //Limpiar atributos del modal de insumos elaborados 
        $this->reset('insumos_elaborados', 'search_insumo_elaborado');
    }

    public function eliminarArticulo($index)
    {
        unset($this->form->insumos_elaborados[$index]);
    }


    public function calcularTotalElaborado($key)
    {
        $this->form->calcularTotalElaborado($key);
    }

    public function calcularTotal($i_insumo_receta)
    {
        //Obtener el insumo de la receta a editar
        $insumo = $this->insumos_receta[$i_insumo_receta];
        if (!$insumo['cantidad_c_merma']) {
            $insumo['cantidad_c_merma'] = 1;
            $insumo['cantidad'] = 1;
        } elseif (!$insumo['cantidad']) {
            $insumo['cantidad'] = $insumo['cantidad_c_merma'];
        }
        //Calcular el nuevo total
        $insumo['total'] = round($insumo['cantidad_c_merma'] * $insumo['ingrediente']['costo_con_impuesto'], 2);
        //Reemplazar el insumo
        $this->insumos_receta[$i_insumo_receta] = $insumo;
    }

    /**
     * Prepara el componente para el modo de edicion de la receta\
     * Consulta la receta del insumo elaborado y abre el modal
     */
    public function editableReceta($index)
    {
        //Guardar temporalmente (en el componente) el insumo editable
        $this->insumo_editable = $this->form->insumos_elaborados[$index];
        $this->index_editable = $index;

        //Obtener la receta a editar de forma temporal y agregar las existencias (insumos)
        $receta = $this->form->actualizarExistencias($index, $this->fecha_existencias, $this->hora_existencias, $this->clave_origen);

        //Agregar la propiedad del checkbox
        $receta = array_map(function ($insum) {
            $insum['selected'] = false;
            return $insum;
        }, $receta);
        //Guardar temporalmente la receta para modificar (Modo edicion)
        $this->insumos_receta = $receta;
        //Emitir evento para abrir modal
        $this->dispatch('open-modal', name: 'modal-receta');
    }

    /**
     * Elimina un elemento de la receta del insumo elaborado\
     * Dado el indice como parametro
     */
    public function eliminarReceta($index)
    {
        unset($this->insumos_receta[$index]);
    }

    /**
     * Reemplaza la receta original, con el nuevo valor
     */
    public function aceptarEdicion()
    {
        //Validar que los valores de la receta, no sean vacios
        $this->validarReceta();
        //Guardar cambios
        $this->form->reemplazarReceta($this->insumos_receta, $this->index_editable);
        //emitir evento para cerrar modal
        $this->dispatch('close-modal');
        //Limpiar propiedades editables
        $this->reset('insumo_editable', 'insumos_receta', 'index_editable');
    }

    /**
     * Contiene las reglas para validar las cantidades de la receta.\
     */
    public function validarReceta()
    {
        foreach ($this->insumos_receta as $index => $value) {
            $this->validate([
                'insumos_receta.' . $index . '.cantidad' => 'required|numeric|min:0',
                'insumos_receta.' . $index . '.cantidad_c_merma'  => 'required|numeric|min:0'
            ], [
                'insumos_receta.*.cantidad.required' => 'Obligatorio',
                'insumos_receta.*.cantidad.numeric' => 'Numero',
                'insumos_receta.*.cantidad.min' => 'Mínimo: 0',
                'insumos_receta.*.cantidad_c_merma.required' => 'Obligatorio',
                'insumos_receta.*.cantidad_c_merma.numeric' => 'Numero',
                'insumos_receta.*.cantidad_c_merma.min' => 'Mínimo: 0',
            ]);
        }
    }

    public function guardar()
    {
        //validar parametros generales de la produccion
        $validated = $this->validate([
            'fecha_existencias' => 'required',
            'hora_existencias' => 'required',
            'clave_origen' => 'required',
            'clave_destino' => 'required',
        ]);
        //Agregar las observaciones
        $validated['observaciones'] = $this->observaciones;
        try {
            DB::transaction(function () use ($validated) {
                $trans = $this->form->guardarProduccion($validated);
                $this->form->crearSalida($trans);
                $this->form->crearEntrada($trans);
                //Limpiar componente
                $this->reset(
                    'observaciones',
                    'insumos_elaborados',
                    'search_insumo_elaborado',
                    'clave_origen',
                    'clave_destino',
                    'locked_bodegas',
                    'insumo_editable',
                    'index_editable',
                    'insumos_receta',
                );
                //Limpiar formulario
                $this->form->limpiar();
            });
            session()->flash('success', 'Produccion registrada correctamente!');
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        $this->dispatch('open-action-message');
    }

    public function render()
    {
        return view('livewire.almacen.produccion.nueva-produccion');
    }
}

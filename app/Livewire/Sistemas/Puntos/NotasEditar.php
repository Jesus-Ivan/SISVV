<?php

namespace App\Livewire\Sistemas\Puntos;

use App\Constants\PuntosConstants;
use App\Livewire\Forms\VentaEditarForm;
use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\MotivoCorreccion;
use App\Models\PuntoVenta;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class NotasEditar extends Component
{
    //Datos generales para la correcion
    public $solicitante_id, $motivo_id;
    //Para las cortesias
    public $observaciones;
    //Datos editables
    public $venta = [], $productos = [], $pagos = [];

    //Nuevo socio
    public $nuevo_socio;

    //Formulario de operaciones
    public VentaEditarForm $editarForm;

    //Setear el valor obtenido desde del controlador
    public function mount($folio)
    {
        //Consultas a la BD
        $venta = Venta::find($folio)
            ->toArray();
        $productos = DetallesVentaProducto::with('catalogoProductos')
            ->where('folio_venta', $folio)
            ->get()
            ->toArray();
        $pagos = DetallesVentaPago::where('folio_venta', $folio)
            ->get()
            ->toArray();

        //Setear valores editables
        $this->venta = $venta;
        $this->productos =  $productos;
        $this->pagos = $pagos;

        //Resguardar los originales
        $this->editarForm->setOriginal($venta, $productos, $pagos);
    }

    #[Computed()]
    public function cajas()
    {
        return Caja::with('users')
            ->where('clave_punto_venta', $this->venta['clave_punto_venta'])
            ->orderBy('fecha_apertura', 'DESC')
            ->limit(35)
            ->get();
    }

    //Calcula el total de la tabla de productos
    #[Computed()]
    public function total_productos()
    {
        $productos = array_filter($this->productos, function ($producto) {
            return ! array_key_exists('deleted', $producto);
        });
        return array_sum(array_column($productos, 'subtotal'));
    }

    //Calcula el total de la tabla de pagos
    #[Computed()]
    public function total_pagos()
    {
        $pagos = array_filter($this->pagos, function ($pago) {
            return ! array_key_exists('deleted', $pago);
        });
        return array_sum(array_column($pagos, 'monto'));
    }

    #[On('on-selected-socio')]
    public function onSelectedSocio(Socio $socio)
    {
        $this->nuevo_socio = $socio->toArray();
    }

    /**
     * Funcion que abre el modal de 'cajas'. En cada peticion se actualiza la propiedad computarizada que lo rellena
     */
    public function searchCajas()
    {
        //Abrir modal de cajas
        $this->dispatch('open-modal', name: 'modalCortes');
    }
    /**
     * Cambia la el corte de caja actual de la venta, por el seleccionado
     */
    public function selectCaja($corte)
    {
        //Actualizamos el nuevo corte de caja de la venta
        $this->venta['corte_caja'] = $corte;
        //Cerrar modal
        $this->dispatch('close-modal');
    }


    public function confirmarCortesia()
    {
        $validated = $this->validate([
            'observaciones' => 'required',
            'venta' => 'required|min:1',
            'solicitante_id' => 'required',
            'motivo_id' => 'required',
        ]);
    
        try {
            DB::transaction(function () use ($validated) {
                //Convertir en cortesia
                $this->editarForm->cortesia($validated['venta']['folio'], $validated['observaciones']);
                //Crear el registro de la bitacora
                $this->editarForm->registrarCorreccion($validated['venta'], $validated['solicitante_id'], $validated['motivo_id']);
            });
            //Redirigir al usuario a la pantalla principal
            $this->redirectRoute('sistemas.pv.notas');
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());    //Mensaje de session error
            $this->dispatch('open-action-message');         //abrir el alert
        } finally {
            $this->dispatch("close-modal");                 //Cerrar el dialogo de cortesia
        }
    }

    public function guardarCambios()
    {
        $validated = $this->validateCorreccion();

        try {
            DB::transaction(function () use ($validated) {
                //Guardamos los cambios del punto de venta
                $this->editarForm->actualizarPunto($validated['venta']);
                //Cambiar el socio titular
                $this->editarForm->actualizarSocioTitular($validated['venta'], $this->nuevo_socio);
                //Actualizar los productos
                $this->editarForm->actualizarProductos($this->productos);
                $this->editarForm->actualizarTotal($this->total_productos);
                //Actualizar los metodos de pago
                $this->editarForm->actualizarPagos($this->pagos);

                //Crear el registro de la bitacora
                $this->editarForm->registrarCorreccion($validated['venta'], $validated['solicitante_id'], $validated['motivo_id']);
            });

            //Redirigir al usuario a la pantalla principal
            $this->redirectRoute('sistemas.pv.notas');
        } catch (\Throwable $th) {
            //Mensaje de session
            session()->flash('fail', $th->getMessage());
            //evento para abrir el action message
            $this->dispatch('open-action-message');
        }
    }

    //Limpia el socio titular nuevo, que se selecciono
    public function limpiarTitular()
    {
        $this->reset('nuevo_socio');
    }
    //Elimina el producto de la tabla
    public function eliminarProducto($index)
    {
        //Marcar el producto como eliminado
        $this->productos[$index]['deleted'] = true;
    }

    public function eliminarPago($index)
    {
        $this->pagos[$index]['deleted'] = true;
    }

    public function buscarSocioPago($index)
    {
        //Buscar al socio
        $socio = Socio::find($this->pagos[$index]['id_socio']);
        //Si existe
        if ($socio) {
            //Cambiar al socio del metodo de pago
            $this->pagos[$index]['nombre'] = $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m;
        } else {
            //Si no existe, mostrar el mensaje de error
            session()->flash('fail', 'No existe el socio');
            //evento para abrir el action message
            $this->dispatch('open-action-message');
            //Reestablecer el valor original del id
            $this->pagos[$index]['id_socio'] = $this->editarForm->pagos[$index]['id_socio'];
            //Reestablecer el nombre original
            $this->pagos[$index]['nombre'] = $this->editarForm->pagos[$index]['nombre'];
        }
    }

    public function eliminarNota()
    {
        $validated = $this->validateCorreccion();

        try {
            DB::transaction(function () use ($validated) {
                //Eliminar la nota
                $this->editarForm->eliminarNota($validated['venta']);
                //Crear el registro de la bitacora
                $this->editarForm->registrarCorreccion($validated['venta'], $validated['solicitante_id'], $validated['motivo_id']);
            });

            //Mensaje de session
            session()->flash('success', 'Nota eliminada con exito');
            //Redirigir al usuario a la pantalla principal
            $this->redirectRoute('sistemas.pv.notas');
        } catch (\Throwable $th) {
            //Mensaje de session
            session()->flash('fail', $th->getMessage());
            //Evento para el action message
            $this->dispatch('open-action-message');
        }
    }

    private function validateCorreccion()
    {
        $validated = $this->validate([
            'venta' => 'required|min:1',
            'solicitante_id' => 'required',
            'motivo_id' => 'required',
        ]);
        return $validated;
    }

    public function render()
    {
        $users = User::all()->toArray();
        $puntos = PuntoVenta::all()->toArray();
        $tipos_pago = TipoPago::all()->toArray();
        $motivos_correccion = MotivoCorreccion::all()->toArray();
        return view('livewire.sistemas.puntos.notas-editar', [
            'users' => $users,
            'puntos' => $puntos,
            'tipos_pago' => $tipos_pago,
            'motivos_correccion' => $motivos_correccion
        ]);
    }
}

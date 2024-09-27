<?php

namespace App\Livewire\Sistemas\Puntos;

use App\Models\ConversionCortesia;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Cortesias extends Component
{
    use WithPagination;
    //Informacion de busqueda
    public $tipo_busqueda = 'FOL', $fecha_busqueda, $folio_busqueda;
    //Folio seleccionado para transformar
    public $folio_seleccionado = null;
    public $observaciones;

    #[Computed()]
    public function ventas()
    {
        if ($this->tipo_busqueda == 'FOL') {
            return Venta::where('folio', 'like', '%' . $this->folio_busqueda . '%')
                ->take(25)
                ->get();
        } else {
            return Venta::whereDate('fecha_apertura', $this->fecha_busqueda)
                ->take(25)
                ->get();
        }
    }

    public function buscar()
    {
        //reseteamos el paginador
        $this->resetPage();
    }


    public function editarVenta($folio)
    {
        $this->folio_seleccionado = $folio;
        $this->dispatch('open-modal',  name: 'modalObservaciones'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
    }
    public function confirmarCortesia()
    {
        //Validamos los parametros de entrada
        $validated = $this->validate(
            [
                'folio_seleccionado' => 'required',
                'observaciones' => 'required',
            ]
        );
        //Intentamos hacer los cambios
        try {
            //Iniciar transaccion
            DB::transaction(function () use ($validated) {
                $result = Venta::find($validated['folio_seleccionado']);    //Buscar datos generales, en la tabla 'ventas'
                if (!$result) {
                    //Si no hay registros, lanzar error
                    throw new Exception("No se encontro la venta " . $validated['folio_seleccionado']);
                }
                //Buscar los productos de la venta
                $detalles_productos = DetallesVentaProducto::where('folio_venta', $validated['folio_seleccionado'])
                    ->get();
                //Buscar los detalles de pago
                $detalles_pagos = DetallesVentaPago::where('folio_venta', $validated['folio_seleccionado'])
                    ->get();
                //Crear registro en la tabla 'conversiones_cortesias'
                ConversionCortesia::create([
                    'user_name' => auth()->user()->name,
                    'folio_venta' => $result->folio,
                    'tipo_venta' => $result->tipo_venta
                ]);

                //Actualizar el tipo de venta, total y observaciones
                $result->tipo_venta = 'cortesia';
                $result->total = 0;
                $result->observaciones = $validated['observaciones'];
                $result->save();
                //Actualizar los productos
                foreach ($detalles_productos as $key => $producto) {
                    $producto->subtotal = 0;
                    $producto->save();
                }
                //Actualizar el metodo de pago
                foreach ($detalles_pagos as $key => $pago) {
                    $pago->id_tipo_pago = 1;
                    $pago->monto = 0;
                    $pago->save();
                }
                //Comprobar si la venta tiene id_socio
                if ($result->id_socio) {
                    //Obtenemos los ID de los pagos de la venta, en la tabla 'detalles_ventas_pagos'
                    $id_pagos = array_column($detalles_pagos->toArray(), 'id');
                    //Buscar los registros correspondientes de pagos de la venta, en el estado de cuenta
                    $estado_cuenta = EstadoCuenta::whereIn('id_venta_pago', $id_pagos)->get();
                    //Actualizamos el estado de cuenta, para la cortesia
                    foreach ($estado_cuenta as $key => $row) {
                        $row->cargo = 0;
                        $row->abono = 0;
                        $row->saldo = 0;
                        $row->save();
                    }
                }
            }, 2);
            session()->flash('success', 'Cortesia ' . $validated['folio_seleccionado'] . ' aplicada correctamente');
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //limpiamos propiedades
        $this->reset('folio_seleccionado', 'observaciones');
        $this->dispatch('close-modal');         //Evento para cerrar modal
        $this->dispatch('open-action-message'); //evento para abrir el action message
    }
    public function render()
    {
        return view('livewire.sistemas.puntos.cortesias');
    }
}

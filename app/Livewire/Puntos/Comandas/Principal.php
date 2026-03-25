<?php

namespace App\Livewire\Puntos\Comandas;

use App\Constants\PuntosConstants;
use App\Models\DetallesVentaProducto;
use App\Models\PuntoVenta;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Principal extends Component
{

    public $search = "", $fecha = "";
    public $selected_puntos = [];

    #[Computed()]
    public function puntos()
    {
        return PuntoVenta::where('inventariable', 1)->get();
    }

    #[Computed()]
    public function productos()
    {
        $productos_result = DetallesVentaProducto::with(['venta.puntoVenta', 'EstadoProductoVenta'])
            ->whereHas('venta', function ($query) {
                $query->whereAny(
                    ['id_socio', 'nombre'],
                    'LIKE',
                    "%$this->search%"
                );
                $query->whereIn('clave_punto_venta', array_keys($this->selected_puntos));
            })
            ->whereDate('inicio', $this->fecha)
            ->whereNotNull('id_estado')
            ->orderby('inicio', 'DESC')
            ->get();
        return $productos_result;
    }

    public function mount()
    {
        $this->fecha = now()->toDateString();
    }

    public function getListeners()
    {
        return [
            "echo:comandas,.nueva-comanda" => 'render',
            "echo:comandas,.reimpresion-comanda" => 'render',
            "echo:comandas,.error-impresion" => 'handleEventError',
            "echo:comandas,.comanda-lista" => 'handleComandaLista', //Este evento deberia ser para cocina
            "echo:comandas,.comanda-modificada" => 'render'
        ];
    }

    public function handleEventError($payload)
    {
        $venta = $payload['venta'];
        //Si 'clave_punto_venta' de la venta erronea, se encuentra en el array de puntos de venta seleccionados
        if (array_key_exists($venta['clave_punto_venta'], $this->selected_puntos)) {
            // Despachamos un evento simple de navegador (reproducir audio)
            $this->dispatch('play-error-sound', $venta);
        }
    }

    public function handleComandaLista($payload)
    {
        $productos = $payload['productos'];

        foreach ($productos as $clave_punto_venta => $producto) {
            if (array_key_exists($clave_punto_venta, $this->selected_puntos)) {
                // Despachamos un evento simple de navegador (reproducir audio)
                $this->dispatch('play-success-sound');
                break;
            }
        }
    }

    public function buscar()
    {
        $puntos_filtrados = array_filter($this->selected_puntos, function ($item) {
            return $item;
        });
        $this->selected_puntos = $puntos_filtrados;
    }

    public function render()
    {
        return view(
            'livewire.puntos.comandas.principal',
            [
                'estado_en_cola' => PuntosConstants::ID_ESTADO_PRODUCTO_COLA,
                'estado_impreso' => PuntosConstants::ID_ESTADO_PRODUCTO_IMPRESO,
                'estado_listo' => PuntosConstants::ID_ESTADO_PRODUCTO_LISTO,
                'estado_error' => PuntosConstants::ID_ESTADO_PRODUCTO_ERROR,
                'estado_cancelado' => PuntosConstants::ID_ESTADO_PRODUCTO_CANCELADO,
            ]
        );
    }
}

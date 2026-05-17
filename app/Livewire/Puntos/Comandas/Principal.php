<?php

namespace App\Livewire\Puntos\Comandas;

use App\Constants\PuntosConstants;
use App\Models\DetallesVentaProducto;
use App\Models\PuntoVenta;
use App\Models\ZonaImpresion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Principal extends Component
{

    public $search = "", $fecha = "";
    public $selected_puntos = [];
    public $codigopv;

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

    #[Computed()]
    public function ordenes()
    {
        /**
         * Consulta de los productos
         */
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
            ->orderby('inicio', 'ASC')
            ->get()
            ->groupBy(['inicio', 'folio_venta']);

        /**
         * Estructura de datos personalizada
         */
        $comandas = []; //Array para almacenar las comandas a renderizar
        foreach ($productos_result as $inicio => $ventas) {

            //Auxiliar para la estructura de datos
            $comanda_aux['inicio'] = $inicio;

            //Para cada venta, extraer la informacion
            foreach ($ventas as $folio => $productos) {
                $prod = $productos->toArray();
                $comanda_aux['detalles'] = $prod;
                $comanda_aux['venta'] = $prod[0]['venta'];
            }
            array_push($comandas, $comanda_aux);
        }

        /**
         * Paginacion
         */
        $perPage = 10;
        // Obtenemos la página actual desde Livewire
        $currentPage = Paginator::resolveCurrentPage('page') ?? 1;
        // Convertimos a Colección y extraemos solo los elementos de la página actual
        $items = collect($comandas)
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->all();
        // Creamos el paginador manualmente
        return new LengthAwarePaginator(
            $items,
            count($comandas),
            $perPage,
            $currentPage,
            ['path' => route('pv.comandas', ['codigopv' => $this->codigopv])] // Importante para que los links funcionen
        );
    }

    public function mount()
    {
        $this->fecha = now()->toDateString();
    }

    public function getListeners()
    {
        $zonas = ZonaImpresion::all();
        $listeners = [];

        //Crear los listeners por zona
        foreach ($zonas as $key => $zona) {
            $listeners["echo:comandas-$zona->id,.comanda-details"] = 'handleEventDetails';
        }
        //Agregar el listener general
        $listeners["echo:comandas-general,.comanda-lista"] = 'handleComandaLista';

        return $listeners;
    }

    public function handleEventDetails($payload)
    {
        $type = $payload['type'];
        $venta = $payload['venta'];

        switch ($type) {
            case PuntosConstants::COMANDA_NUEVA_EVENT:
                //render
                break;
            case PuntosConstants::COMANDA_ERROR_EVENT:
                $this->handleEventError($venta);
                break;
            case PuntosConstants::COMANDA_REIMP_EVENT:
                //render
                break;
            case PuntosConstants::COMANDA_ACTUALIZADA_EVENT:
                //render
                break;
        }
    }

    public function handleEventError($venta)
    {
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

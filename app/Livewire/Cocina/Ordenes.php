<?php

namespace App\Livewire\Cocina;

use App\Constants\PuntosConstants;
use App\Events\ComandaLista;
use App\Jobs\ReimprimirComandaJob;
use App\Models\DetallesVentaProducto;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Ordenes extends Component
{
    use WithPagination;
    public $fecha_busqueda = '';

    //Hook al inicio del vida del componente
    public function mount()
    {
        $this->fecha_busqueda = now()->format('Y-m-d');
        //$this->fecha_busqueda = "2026-02-13";
    }

    // Hook que se ejecuta despues de actualizar la pagina (paginacion)
    public function updatedPage($page)
    {
        $this->dispatch('clear-ids');
    }

    #[Computed()]
    public function ordenes()
    {
        /**
         * Consulta de los productos
         */
        $productos_result = DetallesVentaProducto::with(['venta.puntoVenta'])
            ->whereDate('inicio', $this->fecha_busqueda)
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
            ['path' => route('cocina.ordenes')] // Importante para que los links funcionen
        );
    }

    public function getListeners()
    {
        return [
            "echo:comandas,.nueva-comanda" => 'handleNuevaComanda',
            "echo:comandas,.error-impresion" => 'handleEventError',
            "echo:comandas,.reimpresion-comanda" => 'render',
            "echo:comandas,.comanda-modificada" => 'handleModif'
        ];
    }

    public function handleEventError($payload)
    {
        $venta = $payload['venta'];
        // Despachamos un evento simple de navegador (reproducir audio)
        $this->dispatch('play-error-sound', $venta);
    }

    public function handleNuevaComanda($payload)
    {
        $venta = $payload['venta'];
        // Despachamos un evento simple de navegador (reproducir audio)
        $this->dispatch('play-standard-sound', $venta);
    }

    public function handleModif($payload)
    {
        $message = "Comanda " . $payload['venta']['folio'] . " modificada: " . $payload['venta']['nombre'];
        $this->dispatch('show-modif', $message);
    }

    public function buscar()
    {
        $this->resetPage();
        $this->dispatch('clear-ids');
    }

    public function reimprimirComanda($folio, $inicio)
    {
        //Creamos JOB para la impresora de red.
        ReimprimirComandaJob::dispatch($folio, Carbon::parse($inicio));
    }

    public function confirmarOrdenes(array $value)
    {
        //Obtener los productos seleccionados (desde el frontend)
        $result = DB::table('detalles_ventas_productos')
            ->join('ventas', 'detalles_ventas_productos.folio_venta', '=', 'ventas.folio')
            ->select(
                'detalles_ventas_productos.id',
                'detalles_ventas_productos.folio_venta',
                'detalles_ventas_productos.clave_producto',
                'detalles_ventas_productos.nombre',
                'detalles_ventas_productos.observaciones',
                'detalles_ventas_productos.cantidad',
                'detalles_ventas_productos.id_estado',
                'detalles_ventas_productos.inicio',
                'detalles_ventas_productos.terminado',
                'detalles_ventas_productos.deleted_at',
                'ventas.corte_caja',
                'ventas.clave_punto_venta',
            )
            ->whereNull('detalles_ventas_productos.deleted_at')
            ->whereIn('detalles_ventas_productos.id', $value)
            ->get();
        //Agruparlos por punto de venta    
        $productos = $result->groupBy('clave_punto_venta');
        //Creamos una fecha de terminado para los detalles de los productos
        $terminado = now()->format('Y-m-d H:i:s');

        //Actualizar el estado de los productos
        DB::transaction(function () use ($value, $terminado) {
            DetallesVentaProducto::whereIn('id', $value)
                ->update([
                    'id_estado' => PuntosConstants::ID_ESTADO_PRODUCTO_LISTO,
                    'terminado' => $terminado
                ]);
        }, 2);

        //Avisamos en tiempo real que la comanda esta lista
        broadcast(new ComandaLista($productos));

        //Eventos para alphine js
        //$this->dispatch('clear-ids');
        $this->dispatch('comandas-confirmadas', $value);
    }

    public function render()
    {
        return view('livewire.cocina.ordenes', [
            'estado_en_cola' => PuntosConstants::ID_ESTADO_PRODUCTO_COLA,
            'estado_impreso' => PuntosConstants::ID_ESTADO_PRODUCTO_IMPRESO,
            'estado_listo' => PuntosConstants::ID_ESTADO_PRODUCTO_LISTO,
            'estado_error' => PuntosConstants::ID_ESTADO_PRODUCTO_ERROR,
            'estado_cancelado' => PuntosConstants::ID_ESTADO_PRODUCTO_CANCELADO,
        ]);
    }
}

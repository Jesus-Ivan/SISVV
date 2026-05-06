<?php

namespace App\Jobs;

use App\Constants\PuntosConstants;
use App\Events\ComandaDetails;
use App\Models\DetallesVentaProducto;
use App\Models\Venta;
use App\Services\TicketPrinterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Throwable;

class ImprimirComandaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $folio_venta;

    // El número de reintentos si algo falla (ej. impresora ocupada) (3 tri)
    public $tries = 3;
    // Segundos a esperar antes de reintentar (5 seg)
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct($folio)
    {
        $this->folio_venta = $folio;
    }

    /**
     * Execute the job.
     */
    public function handle(TicketPrinterService $printerService): void
    {
        //Buscar la informacion de la venta
        $venta = Venta::with(['puntoVenta'])
            ->find($this->folio_venta);
        //Obtener productos de venta (en cola)
        $productos_result = DetallesVentaProducto::with('zonaImpresion')
            ->where('folio_venta', $this->folio_venta)
            ->where('id_estado', PuntosConstants::ID_ESTADO_PRODUCTO_COLA)
            ->get();
        $prod = $productos_result->groupBy('id_zona');


        foreach ($prod as $id_zona => $collection) {
            $zona = $collection[0]->zonaImpresion;
            if (!$id_zona || !$zona) continue;

            try {
                $printerService->imprimirComanda($collection, $venta, $zona);

                $this->actualizarEstado($collection, PuntosConstants::ID_ESTADO_PRODUCTO_IMPRESO);

                //Avisamos en tiempo real La comanda nueva (si hay al menos 1 producto en cola, impreso)
                if (count($collection) > 0)
                    broadcast(new ComandaDetails(PuntosConstants::COMANDA_NUEVA_EVENT, $venta, $zona));
            } catch (Throwable $e) {

                $this->actualizarEstado($collection, PuntosConstants::ID_ESTADO_PRODUCTO_ERROR);

                //Avisamos en tiempo real (el error de la impresora de cocina)
                broadcast(new ComandaDetails(PuntosConstants::COMANDA_ERROR_EVENT, $venta, $zona, $e->getMessage()));
            }
        }
    }

    /**
     * Cambia el estado de los productos, en la tabla 'detalles_ventas_productos'
     */
    private function actualizarEstado(Collection $coll_productos, string $id_estado)
    {
        DB::transaction(function () use ($coll_productos, $id_estado) {
            foreach ($coll_productos as $key => $p) {
                if ($p->id_zona) {
                    $p->id_estado = $id_estado;
                    $p->save();
                }
            }
        }, 2);
    }
}

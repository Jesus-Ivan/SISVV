<?php

namespace App\Jobs;

use App\Constants\PuntosConstants;
use App\Events\ErrorImpresora;
use App\Events\NuevaComanda;
use App\Models\DetallesVentaProducto;
use App\Models\Venta;
use App\Services\TicketPrinterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

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
        $productos_result = DetallesVentaProducto::where('folio_venta', $this->folio_venta)
            ->where('id_estado', PuntosConstants::ID_ESTADO_PRODUCTO_COLA)
            ->get();
        try {
            $printerService->imprimirComanda($productos_result, $venta);

            //Actualizar registros
            DB::transaction(function () use ($productos_result) {
                foreach ($productos_result as $key => $p) {
                    $p->id_estado = PuntosConstants::ID_ESTADO_PRODUCTO_IMPRESO;
                    $p->save();
                }
            }, 2);
            //Avisamos en tiempo real La comanda nueva (si hay al menos 1 producto en cola, impreso)
            if (count($productos_result) > 0)
                broadcast(new NuevaComanda($venta));
        } catch (\Throwable $th) {
            //Actualizar registros en caso de error
            DB::transaction(function () use ($productos_result) {
                foreach ($productos_result as $key => $p) {
                    $p->id_estado = PuntosConstants::ID_ESTADO_PRODUCTO_ERROR;
                    $p->save();
                }
            }, 2);

            //Avisamos en tiempo real (el error de la impresora de cocina)
            broadcast(new ErrorImpresora($th->getMessage(), $venta));
        }
    }
}

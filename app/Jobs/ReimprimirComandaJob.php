<?php

namespace App\Jobs;

use App\Constants\PuntosConstants;
use App\Events\ComandaDetails;
use App\Models\DetallesVentaProducto;
use App\Models\Venta;
use App\Services\TicketPrinterService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class ReimprimirComandaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    // El número de reintentos si algo falla (ej. impresora ocupada) (15 tri)
    public $tries = 3;
    // Segundos a esperar antes de reintentar (10 seg)
    public $backoff = 5;


    /**
     * Create a new job instance.
     */
    public function __construct(public $folio_venta, public Carbon $inicio, public string $id_zona) {}

    /**
     * Execute the job.
     */
    public function handle(TicketPrinterService $printerService): void
    {
        //Buscar la informacion de la venta
        $venta = Venta::with(['puntoVenta'])
            ->find($this->folio_venta);
        //Obtener productos de venta (con el mismo folio, la misma marca de inicio, sean imprimibles, en la zona de impresion determinada)
        $productos_result = DetallesVentaProducto::with('zonaImpresion')
            ->where('folio_venta', $this->folio_venta)
            ->whereDate('inicio', $this->inicio->toDateString())
            ->whereTime('inicio', $this->inicio->toTimeString())
            ->whereNotNull('id_estado')
            ->where('id_zona', $this->id_zona)
            ->get();

        try {
            //Obtenemos la zona de impresion, desde los productos
            $zona =  $productos_result[0]->zonaImpresion;
            $printerService->imprimirComanda($productos_result, $venta, $zona);

            //Actualizar registros
            DB::transaction(function () use ($productos_result) {
                foreach ($productos_result as $key => $p) {
                    if (
                        $p->id_estado == PuntosConstants::ID_ESTADO_PRODUCTO_COLA
                        || $p->id_estado == PuntosConstants::ID_ESTADO_PRODUCTO_ERROR
                    ) {
                        $p->id_estado = PuntosConstants::ID_ESTADO_PRODUCTO_IMPRESO;
                        $p->save();
                    }
                }
            }, 2);
            //Avisamos en tiempo real La comanda reimpresa
            broadcast(new ComandaDetails(PuntosConstants::COMANDA_REIMP_EVENT, $venta, $zona,));
        } catch (\Throwable $th) {
            //Actualizar registros en caso de error
            DB::transaction(function () use ($productos_result) {
                foreach ($productos_result as $key => $p) {
                    $p->id_estado = PuntosConstants::ID_ESTADO_PRODUCTO_ERROR;
                    $p->save();
                }
            }, 2);

            //Avisamos en tiempo real (el error de la impresora de cocina)
            broadcast(new ComandaDetails(PuntosConstants::COMANDA_ERROR_EVENT, $venta, $zona, $th->getMessage()));
        }
    }
}

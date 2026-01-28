<?php

namespace App\Jobs;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class ImprimirComandaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $folio_venta;

    // El número de reintentos si algo falla (ej. impresora ocupada)
    public $tries = 15;
    // Segundos a esperar antes de reintentar
    public $backoff = 10;

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
    public function handle(): void
    {
        try {
            //Buscar la informacion de la venta
            $venta = Venta::with(['puntoVenta', 'detallesProductos'])
                ->find($this->folio_venta);
            $connector = new NetworkPrintConnector(config('app.printer_default'), 9100);
            $printer = new Printer($connector);
            /**
             * HEADER
             */
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($venta->puntoVenta->nombre . "\n");
            $printer->setJustification();   // Reset 
            $printer->setEmphasis(false);
            $printer->feed(2);
            $printer->text("VENTA: " . $this->folio_venta . "\n");
            $printer->text("NOMBRE: " . $venta->nombre . "\n");
            $printer->text("ACCION: " . $venta->id_socio . "\n");
            $printer->text("--------------------------------\n");
            /**
             * BODY
             */
            foreach ($venta->detallesProductos as $key => $producto) {
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text($producto->inicio . "\n");
                $printer->setJustification();   // Reset 
                $printer->text($producto->cantidad . " " . $producto->nombre . "\n");
                $printer->text("-" . $producto->observaciones . "\n");
                $printer->feed();
            }
            $printer->feed(3);
            $printer->cut();

            $printer->close();
        } catch (\Throwable $th) {
            // Si falla, el job se reintentará automáticamente según $tries
            throw $th;
        }
    }
}

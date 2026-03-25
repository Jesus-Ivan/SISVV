<?php

namespace App\Services;

use App\Constants\PuntosConstants;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class TicketPrinterService
{
    /**
     * Imprime la comanda en la impresora por defecto del sistema\
     * En caso de imprimir algun producto, devuelve true.
     */
    public function imprimirComanda($productos_result, Venta $venta)
    {
        //Si hay algun producto por imprimir
        if (count($productos_result) > 0) {
            $connector = new NetworkPrintConnector(config('app.printer_default'), 9100);
            $printer = new Printer($connector);
            $f_inicio = Carbon::parse($productos_result[0]->inicio)->format('d-m-Y H:i');
            $line = "------------------------\n";

            /**
             * Configuracion inicial
             */
            $printer->setFont(Printer::FONT_B);
            $printer->setTextSize(2, 2);
            $printer->feed(7);                      //Espacio inicial

            /**
             * Tittle
             */
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($venta->puntoVenta->nombre . "\n");
            $printer->setEmphasis(false);
            $printer->setJustification();           // Reset 
            $printer->feed();

            /**
             * Info. Venta
             */
            $printer->text("ACCION: " . $venta->id_socio . "\n"); //No. Accion
            $printer->text($venta->nombre . "\n");  //Nombre socio
            $printer->text("VENTA: " . $venta->folio . "\n");    //Folio venta
            $printer->text($line);

            /**
             * BODY
             */
            foreach ($productos_result as $key => $producto) {
                $printer->setEmphasis(true);
                $printer->text($producto->cantidad . " " . $producto->nombre . "\n");
                $printer->setEmphasis(false);
                $printer->text("  -" . $producto->observaciones . "\n");
                //Imprimir linea de separacion de producto (basado en el chunk)
                if ($key < count($productos_result) - 1) {
                    $next_prod = $productos_result[$key + 1];
                    if ($producto->chunk != $next_prod->chunk)
                        $printer->text($line);
                }
            }
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($f_inicio . "\n");       //Fecha inicio
            $printer->setJustification();           // Reset 
            $printer->cut();
            $printer->close();
        }
    }
}

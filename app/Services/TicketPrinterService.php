<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\ZonaImpresion;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class TicketPrinterService
{
    /**
     * Imprime la comanda en la impresora
     */
    public function imprimirComanda($productos_result, Venta $venta, ZonaImpresion $zona, $copias = 1)
    {
        //Si hay algun producto por imprimir
        if (count($productos_result) > 0) {
            $connector = new NetworkPrintConnector($zona->ip, 9100, 3);
            $printer = new Printer($connector);
            $f_inicio = Carbon::parse($productos_result[0]->inicio)->format('d-m-Y H:i');
            $line = "--------------------------------\n";

            /**
             * Configuracion inicial
             */
            $printer->setFont(Printer::FONT_B);
            $printer->setTextSize(2, 2);
            $printer->feed(7);                      //Espacio inicial

            //Imprimir x cantidad de veces
            for ($i = 0; $i < $copias; $i++) {
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
                $printer->text("MESERO: " . ($productos_result[0]->mesero ?? '') . "\n"); //Nombre del mesero
                $printer->text("COMENSALES: " . $venta->num_comensales . "\n");    //comensales
                $printer->text($line);

                /**
                 * BODY
                 */
                $productosAgrupados = collect($productos_result)->groupBy(function ($item) {
                    return $item->tiempo ?: '1';
                });
                //Orden fijo: 1, 2, 3, 4
                $ordenTiempos = ['1', '2', '3', '4'];
                foreach ($ordenTiempos as $tiempo) {
                    $grupo = $productosAgrupados->get($tiempo);
                    if (!$grupo) continue;

                    $printer->text($line);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("       Tiempo {$tiempo}       \n");
                    $printer->setJustification();           // Reset
                    $printer->text($line);

                    $grupo = $grupo->values();
                    foreach ($grupo as $key => $producto) {
                        $printer->setEmphasis(true);
                        $printer->text($producto->cantidad . " " . $producto->nombre . "\n");
                        $printer->setEmphasis(false);
                        $printer->text("  -" . $producto->observaciones . "\n");
                        //Imprimir linea de separacion de producto (basado en el chunk)
                        if ($key < $grupo->count() - 1) {
                            $next_prod = $grupo[$key + 1];
                            if ($producto->chunk != $next_prod->chunk)
                                $printer->text($line);
                        }
                    }
                    $printer->text($line);
                }
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($f_inicio . "\n");       //Fecha inicio
                $printer->setJustification();           // Reset 
                $printer->cut();
            }
            $printer->close();
        }
    }
}

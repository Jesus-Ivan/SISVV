<?php

namespace App\Http\Controllers;

use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class VentasController extends Controller
{
    public function generarPDF(Venta $venta)
    {
        $productos = DetallesVentaProducto::where('folio_venta', $venta->folio)->get();
        $pagos = DetallesVentaPago::where('folio_venta', $venta->folio)->get();

        $data = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.DED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA',
            'telefono' => '3745011',
            'folio' => $venta->folio,
            'fecha' => $venta->fecha_apertura,
            'socio_id' => $venta->id_socio,
            'socio_nombre' => $venta->nombre,
            'productos' => $productos,
            'pagos' => $pagos,
            'total' => $venta->total,
        ];

        $pdf = Pdf::loadView('pdf', $data);
        $pdf->setPaper([0, 0, 226.772, 566.929], 'portrait');
        $pdf->setOption('defaultFont', 'Courier');

        return $pdf->stream('documento.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\PuntoVenta;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stringable;

class ReportesController extends Controller
{
    public function generarTicket(Venta $venta)
    {
        //Con 'with(nombre_relacion)' evitamos el problema N+1
        $productos = DetallesVentaProducto::with('catalogoProductos')->where('folio_venta', $venta->folio)->get();
        $pagos = DetallesVentaPago::with('tipoPago')->where('folio_venta', $venta->folio)->get();
        $caja = Caja::with('users')->where('corte', $venta->corte_caja)->limit(1)->get();
        //Enviamos los datos a la vista
        $data = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011',
            'folio' => $venta->folio,
            'fecha' => $venta->fecha_apertura,
            'socio_id' => $venta->id_socio,
            'socio_nombre' => $venta->nombre,
            'productos' => $productos,
            'pagos' => $pagos,
            'total' => $venta->total,
            'caja' => $caja,
        ];

        $altura = $this->calcularAltura($data);

        $pdf = Pdf::loadView('reportes.nota-venta', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 226.772, $altura], 'portrait');

        return $pdf->stream('documento.pdf');
    }

    public function generarCorte(Caja $caja)
    {
        //Buscamos todos los metodos de pago
        $tipos_pago = TipoPago::all();
        //Array de pagos separados por tipo
        $separados = [];
        //Consulta que obtiene los detalles de los pagos con su corte de caja
        $detalles_pago = DB::table('detalles_ventas_pagos')
            ->join('ventas', 'detalles_ventas_pagos.folio_venta', '=', 'ventas.folio')
            ->select('detalles_ventas_pagos.*', 'ventas.corte_caja')
            ->where('corte_caja', $caja->corte)
            ->get();
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            $separados[$pago->descripcion] = $detalles_pago->where('id_tipo_pago', $pago->id);
        }

        $data = [
            'caja' => $caja,
            'detalles_pagos' => $separados
        ];

        $pdf = Pdf::loadView('reportes.ventas', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('corte.pdf');
    }

    public function generarEstadoCuenta( $socio, $tipo, $year, $month)
    {
        //Buscamos el socio
        $resultSocio = Socio::find($socio);

        switch ($tipo) {
            case 'T':
                //Obtenemos todos los conceptos del estado-cuenta correspondiente al socio
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereMonth('fecha', $month)
                    ->whereYear('fecha', $year)
                    ->get();
                break;
            case 'P':
                //Obtenemos los conceptos pendientes (de pagar) del estado-cuenta correspondiente al socio
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereMonth('fecha', $month)
                    ->whereYear('fecha', $year)
                    ->where('saldo', '>', 0)
                    ->get();
                break;
            case 'C':
                //Obtenemos los consumos del socio (esten o no esten pagados)
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereMonth('fecha', $month)
                    ->whereYear('fecha', $year)
                    ->where('consumo', true)
                    ->get();
                break;
        }

        $data = [
            'resultSocio' => $resultSocio,
            'month' => $this->getMes($month),
            'year' => $year,
            'resulEstado' => $resulEstado,
        ];

        $pdf = Pdf::loadView('reportes.estado-cuenta', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('estado-cuenta.pdf');
    }

    private function calcularAltura($data)
    {
        $font_size_p = 7;
        $line = 6;

        $header_ticket = (76);

        $inicio_ticket = (3 * $font_size_p) + (ceil(mb_strlen($data['socio_nombre']) / 25) * $font_size_p) + $line;

        $productos_ticket = 10;
        foreach ($data['productos'] as $key => $producto) {
            $letras = mb_strlen($producto->catalogoProductos->nombre);
            $productos_ticket = $productos_ticket + ((ceil($letras / 23)) * $font_size_p);
        }

        $pago_ticket = 20 + $line;
        foreach ($data['pagos'] as $key => $pago) {
            $letras = mb_strlen($pago->nombre);
            $pago_ticket = $pago_ticket + ((ceil($letras / 26)) * $font_size_p);
        }
        $footer_ticket = ($font_size_p * 2) + 10;
        return  $header_ticket + $inicio_ticket + $productos_ticket + $pago_ticket + $footer_ticket;
    }

    //Recibe un numero de mes y devuelve el mes en español
    private function getMes($fecha)
    {
        switch ($fecha) {
            case 1:
                return 'Enero';
            case 2:
                return 'Febrero';
            case 3:
                return 'Marzo';
            case 4:
                return 'Abril';
            case 5:
                return 'Mayo';
            case 6:
                return 'Junio';
            case 7:
                return 'Julio';
            case 8:
                return 'Agosto';
            case 9:
                return 'Septiembre';
            case 10:
                return 'Octubre';
            case 11:
                return 'Noviembre';
            case 12:
                return 'Diciembre';
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\Recibo;
use App\Models\SaldoFavor;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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

        //$altura = $this->calcularAltura($data);

        $pdf = Pdf::loadView('reportes.nota-venta', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica']);
        //Tamaño predeterminado de papel del ticket (80mm x 297mm)
        $pdf->setPaper([0, 0, 226.772, 841.89], 'portrait');
        return $pdf->stream('venta.pdf');
    }

    //Genera reportes de ventas, con ayuda del corte de caja
    public function generarCorte(Caja $caja)
    {
        //Quitamos los metodos de pago no permitidos.
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'TRANSFERENCIA')
                ->orWhere('descripcion', 'like', 'DEPOSITO')
                ->orWhere('descripcion', 'like', 'CHEQUE')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();
        //Array auxiliar de pagos separados por tipo
        $separados = [];
        //Consulta que obtiene los detalles de los pagos con su corte de caja
        $detalles_pago = DB::table('detalles_ventas_pagos')
            ->join('ventas', 'detalles_ventas_pagos.folio_venta', '=', 'ventas.folio')
            ->select('detalles_ventas_pagos.*', 'ventas.corte_caja')
            ->where('corte_caja', $caja->corte)
            ->get();

        //Obtenemos el total del corte
        $totalVenta = array_sum(array_column($detalles_pago->toArray(), 'monto'));
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            $separados[$pago->descripcion] = $detalles_pago->where('id_tipo_pago', $pago->id);
        }
        //Almacenamos la informacion en un array, para la vista del resporte
        $data = [
            'caja' => $caja,
            'detalles_pagos' => $separados,
            'totalVenta' => $totalVenta
        ];

        $pdf = Pdf::loadView('reportes.ventas', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream("corte{{$caja->corte}}.pdf");
    }

    public function generarEstadoCuenta($socio, $tipo, $vista, $fInicio, $fFin, $option)
    {
        //Buscamos el socio
        $resultSocio = Socio::find($socio);
        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];

        switch ($tipo) {
            case 'T':
                //Obtenemos todos los conceptos del estado-cuenta correspondiente al socio
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereDate('fecha', '>=', $fInicio)
                    ->whereDate('fecha', '<=', $fFin)
                    ->orderBy('fecha', 'asc')
                    ->get();
                break;
            case 'P':
                //Obtenemos los conceptos pendientes (de pagar) del estado-cuenta correspondiente al socio
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereDate('fecha', '>=', $fInicio)
                    ->whereDate('fecha', '<=', $fFin)
                    ->where('saldo', '>', 0)
                    ->orderBy('fecha', 'asc')
                    ->get();
                break;
            case 'C':
                //Obtenemos los consumos del socio (esten o no esten pagados)
                $resulEstado = EstadoCuenta::where('id_socio', $resultSocio->id)
                    ->whereDate('fecha', '>=', $fInicio)
                    ->whereDate('fecha', '<=', $fFin)
                    ->where('consumo', true)
                    ->orderBy('fecha', 'asc')
                    ->get();
                break;
        }
        if ($vista == 'ORD') {
            $resulEstado = $resulEstado->where('vista', 'ORD');
        } elseif ($vista == 'ESP') {
            $resulEstado = $resulEstado->where('vista', 'ESP');
        }
        //Buscamos si el socio tiene saldo a favor disponible;
        $saldoFavor = DB::table('recibos')
            ->join('saldo_favor', 'recibos.folio', '=', 'saldo_favor.folio_recibo_origen')
            ->select('recibos.id_socio', 'saldo_favor.*')
            ->where('id_socio', $resultSocio->id)
            ->whereNull('aplicado_a')
            ->get();

        $data = [
            'header' => $header,
            'resultSocio' => $resultSocio,
            'fInicio' =>  $fInicio,
            'fFin' =>  $fFin,
            'resulEstado' => $resulEstado,
            'saldoFavor' => $saldoFavor
        ];

        $pdf = Pdf::loadView('reportes.estado-cuenta', $data);
        if ($option == 'd') {
            return $pdf->download("ESTADO-CUENTA-$resultSocio->id-$resultSocio->nombre.pdf");
        }
        return $pdf->stream("ESTADO-CUENTA-$resultSocio->id-$resultSocio->nombre.pdf");
    }

    public function generarRecibo(int $folio)
    {
        $detalles_cobro = DB::table('detalles_recibo')
            ->join('estados_cuenta', 'detalles_recibo.id_estado_cuenta', '=', 'estados_cuenta.id')
            ->join('tipos_pago', 'detalles_recibo.id_tipo_pago', '=', 'tipos_pago.id')
            ->select('detalles_recibo.*', 'estados_cuenta.concepto', 'tipos_pago.descripcion')
            ->where('folio_recibo', '=', $folio)
            ->get();
        $cobro = Recibo::find($folio);
        $saldoFavor = SaldoFavor::where('folio_recibo_origen', '=', $folio)->first();
        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];

        $data = [
            'header' => $header,
            'detalles' => $detalles_cobro,
            'cobro' => $cobro,
            'saldoFavor' => $saldoFavor
        ];

        $pdf = Pdf::loadView('reportes.recibo', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('recibo' . $folio . '.pdf');
    }

    public function generarCobranzaDetalles(Caja $caja)
    {
        //Buscamos los metodos de pago, permitidos para el reporte de cobranza
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'FIRMA')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();

        //Array auxiliar de recibos separados por tipo
        $separados = [];
        //Obtenemos los detalles de recibos unidos con el re
        $detalles = DB::table('recibos')
            ->join('detalles_recibo', 'recibos.folio', '=', 'detalles_recibo.folio_recibo')
            ->select(
                'recibos.folio',
                'recibos.id_socio',
                'recibos.nombre',
                'recibos.total',
                'recibos.corte_caja',
                'recibos.created_at',
                'detalles_recibo.id_estado_cuenta',
                'detalles_recibo.id_tipo_pago',
                'detalles_recibo.saldo_anterior',
                'detalles_recibo.monto_pago',
                'detalles_recibo.saldo',
                'detalles_recibo.saldo',
                'detalles_recibo.saldo_favor_generado',
            )
            ->where('corte_caja', $caja->corte)
            ->get();

        //Variable auxiliar que almacena el total
        $totalCobranza = 0;
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            $separados[$pago->descripcion] = $detalles->where('id_tipo_pago', $pago->id);
            //Acumulamos el total de 'monto_pago', de los cargos recien separados por el tipo de pago
            $totalCobranza += array_sum(array_column($separados[$pago->descripcion]->toArray(), 'monto_pago'));
        }

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];

        $data = [
            'header' => $header,
            'detalles_recibo' => $separados,
            'cobro' => 2,
            'total' => $totalCobranza
        ];

        $pdf = Pdf::loadView('reportes.cobranza-detalles', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('ReporteCobranza.pdf');
    }

    public function generarCobranzaResumen(Caja $caja)
    {
        //Buscamos los metodos de pago, permitidos para el reporte de cobranza
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'FIRMA')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();

        //Variable auxiliar que almacena los totales de los recibos, por metodos de pago
        $arrayAux = [];
        //Obtenemos los detalles de recibos unidos con el recibos
        $detalles_recibo = DB::table('recibos')
            ->join('detalles_recibo', 'recibos.folio', '=', 'detalles_recibo.folio_recibo')
            ->select(
                'recibos.folio',
                'recibos.id_socio',
                'recibos.nombre',
                'recibos.corte_caja',
                'recibos.created_at',
                'detalles_recibo.id_tipo_pago',
                'detalles_recibo.monto_pago',
            )
            ->where('corte_caja', $caja->corte)
            ->get();

        //Recorremos todos los detalles de los recibos
        foreach ($detalles_recibo as $key => $detalle) {
            //Verificamos si existe un elemento en el array con el mismo folio de recibo y tipo de pago
            $countFilter = array_filter($arrayAux, function ($value) use ($detalle) {
                return $value['folio'] == $detalle->folio && $value['id_tipo_pago'] == $detalle->id_tipo_pago;
            });
            //Si existe al menos 1, elemento en el array, con el folio y tipo de pago
            if (count($countFilter) > 0) {
                //Buscamos la posicion del elemento en el array
                for ($i = 0; $i < count($arrayAux); $i++) {
                    if ($arrayAux[$i]['folio'] == $detalle->folio &&  $arrayAux[$i]['id_tipo_pago'] == $detalle->id_tipo_pago) {
                        //Si coincide con folio y tipo de pago, acumulamos el monto, en el elemento del array.
                        $arrayAux[$i]['monto_pago'] += $detalle->monto_pago;
                    }
                }
            } else {
                //Si no existe dentro del array, lo agregamos
                array_push($arrayAux, [
                    'folio' => $detalle->folio,
                    'id_socio' => $detalle->id_socio,
                    'nombre' => $detalle->nombre,
                    'id_tipo_pago' => $detalle->id_tipo_pago,
                    'monto_pago' => $detalle->monto_pago,
                    'corte_caja' => $detalle->corte_caja,
                    'created_at' => $detalle->created_at,
                ]);
            }
        }

        $separados = [];    //Array auxiliar de recibos separados por tipo
        $totalCobranza = 0;  //Acumulador auxiliar
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            //$separados[$pago->descripcion] = $detalles->where('id_tipo_pago', $pago->id);
            $separados[$pago->descripcion] = array_filter($arrayAux, function ($row) use ($pago) {
                return $pago->id == $row['id_tipo_pago'];
            });
            //Acumulamos el total de 'monto_pago', de los cargos recien separados por el tipo de pago
            $totalCobranza += array_sum(array_column($separados[$pago->descripcion], 'monto_pago'));
        }

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];

        $data = [
            'header' => $header,
            'detalles_recibo' => $separados,
            'total' => $totalCobranza
        ];

        $pdf = Pdf::loadView('reportes.cobranza-resumen', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('ReporteCobranzaResumen.pdf');
    }

    public function generarQR($socio)
    {
        $resultSocio = Socio::find($socio);
        $data = [
            'resultSocio' => $resultSocio,
        ];

        $codigoQR = QrCode::size(150)->generate($resultSocio->id);
        $pdf = Pdf::loadView('reportes.qr', $data,  ["valor" => $codigoQR]);
        $pdf->setPaper([0, 0, 226.772, 841.89], 'portrait');
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('qr' . $resultSocio->nombre . '.pdf');
    }

    public function vencidos(Request $request)
    {
        $fInicio = $request->input('fInicio');
        $fFin = $request->input('fFin');

        $this->validate($request, [
            'fInicio' => 'required|date',
            'fFin' => 'required|date',
        ]);

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011',
            'fInicio' => $fInicio,
            'fFin' => $fFin
        ];

        $estados = EstadoCuenta::whereDate('fecha', '>=', $fInicio)
            ->whereDate('fecha', '<=', $fFin)
            ->where('saldo', '>', 0)
            ->get();

        $totales = [];
        foreach ($estados as $key => $estado) {
            $result_filter = array_filter($totales, function ($row) use ($estado) {
                return $row['id_socio'] == $estado->id_socio;
            });

            if (count($result_filter) > 0) {
                //Buscamos la posicion del elemento en el array
                for ($i = 0; $i < count($totales); $i++) {
                    if ($totales[$i]['id_socio'] == $estado->id_socio) {
                        //Si coincide con folio y tipo de pago, acumulamos el monto, en el elemento del array.
                        $totales[$i]['monto'] += $estado->saldo;
                    }
                }
            } else {
                //Si no existe dentro del array
                $socio = Socio::where('id', $estado->id_socio)->limit(1)->get();
                array_push($totales, [
                    'id_socio' => $estado->id_socio,
                    'nombre' => count($socio) > 0 ? $socio[0]->nombre . ' ' . $socio[0]->apellido_p . ' ' . $socio[0]->apellido_m : 'N/R',
                    'monto' => $estado->saldo,
                ]);
            }
        }
        $data = [
            'header' => $header,
            'totales' => $totales,
            'total' => array_sum(array_column($totales, 'monto'))
        ];
        $pdf = Pdf::loadView('reportes.cartera-vencida', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('reporte-vencidos' . $fInicio . '.pdf');
    }

    public function mensual(Request $request)
    {
        $fInicio = $request->input('fechaInicio');
        $fFin = $request->input('fechaFin');
        $type = $request->input('selectedType');

        if ($type == 'V') {
            
        } elseif ($type == 'R') {
            return $this->reporteRecibos($fInicio, $fFin);
        }
    }

    private function reporteRecibos($fInicio, $fFin)
    {
        //Buscamos los metodos de pago, permitidos para el reporte de cobranza
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'FIRMA')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();

        //Variable auxiliar que almacena los totales de los recibos, por metodos de pago
        $arrayAux = [];
        //Obtenemos los detalles de recibos unidos con el recibos
        $detalles_recibo = DB::table('recibos')
            ->join('detalles_recibo', 'recibos.folio', '=', 'detalles_recibo.folio_recibo')
            ->select(
                'recibos.folio',
                'recibos.id_socio',
                'recibos.nombre',
                'recibos.corte_caja',
                'recibos.facturado',
                'recibos.created_at',
                'detalles_recibo.id_tipo_pago',
                'detalles_recibo.monto_pago',
            )
            ->whereDate('created_at', '>=', $fInicio)
            ->whereDate('created_at', '<=', $fFin)
            ->get();


        //Recorremos todos los detalles de los recibos
        foreach ($detalles_recibo as $key => $detalle) {
            //Verificamos si existe un elemento en el array con el mismo folio de recibo y tipo de pago
            $countFilter = array_filter($arrayAux, function ($value) use ($detalle) {
                return $value['folio'] == $detalle->folio && $value['id_tipo_pago'] == $detalle->id_tipo_pago;
            });
            //Si existe al menos 1, elemento en el array, con el folio y tipo de pago
            if (count($countFilter) > 0) {
                //Buscamos la posicion del elemento en el array
                for ($i = 0; $i < count($arrayAux); $i++) {
                    if ($arrayAux[$i]['folio'] == $detalle->folio &&  $arrayAux[$i]['id_tipo_pago'] == $detalle->id_tipo_pago) {
                        //Si coincide con folio y tipo de pago, acumulamos el monto, en el elemento del array.
                        $arrayAux[$i]['monto_pago'] += $detalle->monto_pago;
                    }
                }
            } else {
                //Si no existe dentro del array, lo agregamos
                array_push($arrayAux, [
                    'folio' => $detalle->folio,
                    'id_socio' => $detalle->id_socio,
                    'nombre' => $detalle->nombre,
                    'id_tipo_pago' => $detalle->id_tipo_pago,
                    'monto_pago' => $detalle->monto_pago,
                    'facturado' => $detalle->facturado,
                    'corte_caja' => $detalle->corte_caja,
                    'created_at' => $detalle->created_at,
                ]);
            }
        }

        $separados = [];    //Array auxiliar de recibos separados por tipo
        $totalCobranza = 0;  //Acumulador auxiliar
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            //$separados[$pago->descripcion] = $detalles->where('id_tipo_pago', $pago->id);
            $separados[$pago->descripcion] = array_filter($arrayAux, function ($row) use ($pago) {
                return $pago->id == $row['id_tipo_pago'];
            });
            //Acumulamos el total de 'monto_pago', de los cargos recien separados por el tipo de pago
            $totalCobranza += array_sum(array_column($separados[$pago->descripcion], 'monto_pago'));
        }

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];

        $data = [
            'header' => $header,
            'detalles_recibo' => $separados,
            'total' => $totalCobranza
        ];

        $pdf = Pdf::loadView('reportes.cobranza-resumen', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('ReporteCobranzaResumen.pdf');
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
}

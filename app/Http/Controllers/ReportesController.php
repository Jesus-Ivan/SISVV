<?php

namespace App\Http\Controllers;

use App\Exports\CarteraVencidaExport;
use App\Exports\RecibosExport;
use App\Exports\SociosExport;

use App\Exports\VentasExport;
use App\Models\Caja;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\PuntoVenta;
use App\Models\Recibo;
use App\Models\SaldoFavor;
use App\Models\Socio;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Luecano\NumeroALetras\NumeroALetras;

class ReportesController extends Controller
{
    public function generarTicket(Venta $venta)
    {
        //Con 'with(nombre_relacion)' evitamos el problema N+1
        $productos = DetallesVentaProducto::with('catalogoProductos')->where('folio_venta', $venta->folio)->get();
        $pagos = DetallesVentaPago::with('tipoPago')->where('folio_venta', $venta->folio)->get();
        $caja = Caja::with('users')->where('corte', $venta->corte_caja)->limit(1)->get();
        $puntoVenta = PuntoVenta::where('clave', $venta->clave_punto_venta)->first();
        //Enviamos los datos a la vista
        $data = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011',
            'folio' => $venta->folio,
            'tipo_venta' => $venta->tipo_venta,
            'fecha' => $venta->fecha_apertura,
            'socio_id' => $venta->id_socio,
            'socio_nombre' => $venta->nombre,
            'productos' => $productos,
            'pagos' => $pagos,
            'total' => $venta->total,
            'caja' => $caja,
            'puntoVenta' => $puntoVenta,
        ];

        //$altura = $this->calcularAltura($data);

        $pdf = Pdf::loadView('reportes.nota-venta', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica']);
        //Tamaño predeterminado de papel del ticket (80mm x 297mm)
        $pdf->setPaper([0, 0, 226.772, 841.89], 'portrait');
        return $pdf->stream('venta.pdf');
    }

    //Genera reportes de ventas, con ayuda del corte de caja
    public function generarCorte(Caja $caja, $codigopv = null)
    {
        //Comprobamos si la caja no esta cerrada
        if (!$caja->fecha_cierre && $caja->clave_punto_venta != 'REC') {
            return redirect()->route('home');
        }

        //Quitamos los metodos de pago no permitidos.
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'DEPOSITO')
                ->orWhere('descripcion', 'like', 'CHEQUE')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();
        //Array auxiliar de pagos separados por tipo
        $separados = [];
        //Consulta que obtiene los detalles de los pagos con su corte de caja
        $detalles_pago = DB::table('detalles_ventas_pagos')
            ->join('ventas', 'detalles_ventas_pagos.folio_venta', '=', 'ventas.folio')
            ->select('detalles_ventas_pagos.*', 'ventas.*')
            ->where('corte_caja', $caja->corte)
            ->get();

        //CREAMOS ARRAY DE PUNTOS DE VENTA, PARA LA BUSQUEDA INDEXADA EN LA VIEW DEL REPORTE 'reportes.ventas'
        $puntos_venta = [];
        foreach (PuntoVenta::all()->toArray() as $key => $value) {
            $puntos_venta[$value['clave']] = $value['nombre'];
        }

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
            'totalVenta' => $totalVenta,
            'puntos_venta' => $puntos_venta
        ];

        if ($codigopv) {
            $pdf = Pdf::loadView('reportes.ventas-puntos', $data);
            //Tamaño predeterminado de papel del ticket (80mm x 297mm)
            $pdf->setPaper([0, 0, 226.772, 841.89], 'portrait');
        } else {
            $pdf = Pdf::loadView('reportes.ventas', $data);
        }
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

        $formatter = new NumeroALetras();
        $total_letras = $formatter->toMoney(
            array_sum(array_column($detalles_cobro->toArray(), 'monto_pago')),
            2,
            'PESOS',
            'CENTAVOS'
        );

        $data = [
            'header' => $header,
            'detalles' => $detalles_cobro,
            'cobro' => $cobro,
            'saldoFavor' => $saldoFavor,
            'total_letras' => $total_letras
        ];



        $pdf = Pdf::loadView('reportes.recibo', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792]); // Tamaño aproximado del US LETTER (216 x 279.4) mm
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
                'recibos.facturado',
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

    /**
     * Permite generar una cartera vencida, en diferentes formatos
     */
    public function vencidos(Request $request)
    {
        //Validamos el tipo de archivo
        $validated = $this->validate($request, [
            'typeFile' => 'required',
        ]);
        //Agregamos el key para los consumos del mes
        $validated['consumosMesFin'] = $request->input('consumosMesFin', null);
        //Agregamos el key para indicar si agregar los socios cancelados o no
        $validated['cancelados'] = $request->input('cancelados', null);

        switch ($validated['typeFile']) {
            case 'XLS':
                $result = $this->vencidosExcel($validated['consumosMesFin'], $validated['cancelados']);
                break;
            case 'PDF':
                $result = $this->vencidosPdf($validated['consumosMesFin'], $validated['cancelados']);
                break;
            default:
                $result = 'Algo ha ido mal, prueba denuevo';
                break;
        }

        return $result;
    }

    /**
     * Dado un rango de fechas genera un PDF o XLS, que contiene todos los detalles de pago de las ventas en los puntos;
     * Folio de venta, socio, nombre, tipo de pago, monto, fecha de venta
     */
    public function ventasMes(Request $request)
    {
        $fInicio = $request->input('fechaInicio');
        $fFin = $request->input('fechaFin');
        $type_file = $request->input('type_file');

        //Buscamos las cajas que coincidan entre las fechas
        $cajas = Caja::whereDate('fecha_apertura', '>=', $fInicio)
            ->whereDate('fecha_apertura', '<=', $fFin)
            ->get()
            ->toArray();

        //Quitamos los metodos de pago no permitidos.
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'DEPOSITO')
                ->orWhere('descripcion', 'like', 'CHEQUE')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();

        //REALIZAMOS UNA BUSQUEDA INDEXADA PARA OBTENER EL PUNTO DE VENTA
        $puntos_venta = [];
        foreach (PuntoVenta::all()->toArray() as $key => $value) {
            $puntos_venta[$value['clave']] = $value['nombre'];
        }

        //REALIZAMOS UNA BUSQEUDA INDEXADA PARA OBTENER EL TIPO DE PAGO
        $metodo_pago = [];
        foreach (TipoPago::all()->toArray() as $key => $value) {
            $metodo_pago[$value['id']] = $value['descripcion'];
        }

        //Array auxiliar de pagos separados por tipo
        $separados = [];
        //Consulta que obtiene los detalles de los pagos con su corte de caja
        $detalles_pago = DB::table('detalles_ventas_pagos')
            ->join('ventas', 'detalles_ventas_pagos.folio_venta', '=', 'ventas.folio')
            ->select(
                'ventas.folio',
                'ventas.tipo_venta',
                'ventas.fecha_apertura',
                'ventas.corte_caja',
                'ventas.clave_punto_venta',
                'detalles_ventas_pagos.id_socio', 
                'detalles_ventas_pagos.nombre', 
                'detalles_ventas_pagos.monto', 
                'detalles_ventas_pagos.propina', 
                'detalles_ventas_pagos.id_tipo_pago', 
                )
            ->whereIn('corte_caja', array_column($cajas, 'corte'))
            ->get();
        

        //Obtenemos el total del corte
        $totalVenta = array_sum(array_column($detalles_pago->toArray(), 'monto'));
        //Separar los pagos por tipo
        foreach ($tipos_pago as $pago) {
            $separados[$pago->descripcion] = $detalles_pago->where('id_tipo_pago', $pago->id);
        }

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011',
            'fInicio' => $fInicio,
            'fFin' => $fFin
        ];

        //Almacenamos la informacion en un array, para la vista del resporte
        $data = [
            'header' => $header,
            'detalles_pagos' => $separados,
            'totalVenta' => $totalVenta,
            'puntos_venta' => $puntos_venta
        ];

        if ($type_file == 'PDF') {
            //GENERAMOS EL REPORTE EN PDF
            $pdf = Pdf::loadView('reportes.ventas', $data);
            $pdf->setOption(['defaultFont' => 'Courier']);
            return $pdf->stream("reporteMensual.pdf");
        } else {
            //GENERAMOS EL REPORTE EN EXCEL
            return Excel::download(
                new VentasExport($data, $puntos_venta, $metodo_pago),
                'Ventas - ' . $fInicio . ' - ' . $fFin . '.xlsx'
            );
        }
    }

    /** 
     * Este metodo genera un pdf, donde muestran todos los recibos cobrados por un usuario
     * en un rango de fechas dadas. 
     * Principalmente utilizando en recepcion - reportes.
     */
    public function reporteRecibos(Request $request)
    {
        $fInicio = $request->input('fechaInicio');
        $fFin = $request->input('fechaFin');
        $user = $request->input('user');

        //Buscamos los metodos de pago, permitidos para el reporte de cobranza
        $tipos_pago = TipoPago::whereNot(function (Builder $query) {
            $query->where('descripcion', 'like', 'FIRMA')
                ->orWhere('descripcion', 'like', '%SALDO%');
        })->get();

        //Si se paso un id de usuario
        if ($user) {
            //Buscamos los cortes de caja del usuario
            $cortesResult = Caja::where('id_usuario', $user)
                ->whereDate('fecha_apertura', '>=', $fInicio)
                ->whereDate('fecha_apertura', '<=', $fFin)
                ->get();
        } else {
            //Buscamos todos los cortes de caja, sin importar el usuario
            $cortesResult = Caja::whereDate('fecha_apertura', '>=', $fInicio)
                ->whereDate('fecha_apertura', '<=', $fFin)
                ->get();
        }
        //Obtenemos unicamente el campo de corte de caja.
        $cortes = array_column($cortesResult->toArray(), 'corte');

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
            ->whereIn('corte_caja', $cortes)
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
            'telefono' => '3745011',
            'fInicio' => $fInicio,
            'fFin' => $fFin,
            'usuarioCorte' => User::find($user)
        ];

        $data = [
            'header' => $header,
            'detalles_recibo' => $separados,
            'total' => $totalCobranza
        ];

        $pdf = Pdf::loadView('reportes.cobranza-resumen-mensual', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('ReporteCobranzaResumen.pdf');
    }

    /**
     * Genera un pdf con todos los recibos existentes de un socio determinado
     */
    public function reporteRecibosSocio(Request $request)
    {
        //Obtenemos el id del socio, de la peticion post
        $id_socio = $request->input('no_socio');
        //Obtenemos todos los recibos
        $recibos = Recibo::with('caja')->where('id_socio', $id_socio)->get();

        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011',
        ];

        $data = [
            'id_socio' => $id_socio,
            'header' => $header,
            'recibos' => $recibos,
        ];

        //dd($data);

        $pdf = Pdf::loadView('reportes.recibos-socio', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('Reporte-recibos.pdf');
    }

    /**
     * Metodo que genera un excel para el reporte de los recibos, segun las fechas dadas.
     * El reporte es detallado, utilizado en sistemas - herramientas - reportes
     */
    public function recibosMes(Request $request)
    {
        $fInicio = $request->input('fechaInicio');
        $fFin = $request->input('fechaFin');

        $recibos = Recibo::whereDate('created_at', '>=', $fInicio)
            ->whereDate('created_at', '<=', $fFin)
            ->get();

        //Devolvemos el excel
        return Excel::download(
            new RecibosExport($recibos->toArray()),
            'Reporte recibos ' . $fInicio . ' - ' . $fFin . '.xlsx'
        );
    }

    public function socios(Request $request)
    {
        $hoy = now()->toDateString();
        //Devolvemos el excel
        return Excel::download(
            new SociosExport(),
            'Reporte Socios ' .  $hoy . '.xlsx'
        );
    }

    private function vencidosExcel($consumosMesFin, $cancelados)
    {
        $hoy = now()->toDateString();
        //Devolvemos el excel
        return Excel::download(
            new CarteraVencidaExport($consumosMesFin, $cancelados),
            'Cartera Vencida ' . $hoy . '.xlsx'
        );
    }

    private function vencidosPdf($consumosMesFin, $cancelados)
    {
        $mes_actual = now();
        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];
        if ($consumosMesFin) {
            $estados = EstadoCuenta::where('saldo', '>', 0)
                ->orderBy('id_socio', 'asc')
                ->get();
        } else {
            $estados = EstadoCuenta::where('saldo', '>', 0)
                ->whereNot(function (Builder $query) use ($mes_actual) {
                    $query->whereMonth('fecha', $mes_actual->month)
                        ->whereYear('fecha', $mes_actual->year)
                        ->whereNotNull('id_venta_pago');
                })
                ->orderBy('id_socio', 'asc')
                ->get();
        }


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
                $socio = Socio::with('socioMembresia')->where('id', $estado->id_socio)->first();
                //creamos los datos del socio que se van a agregar a la tabla
                $dataSocio = [
                    'id_socio' => $estado->id_socio,
                    'nombre' => $socio ? $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m : 'N/R',
                    'monto' => $estado->saldo,
                ];
                //Si se selecciono la opcion de incluir cancelados
                if ($cancelados) {
                    array_push($totales, $dataSocio);   //Agregar directamente al array
                } elseif ($socio->socioMembresia->estado != 'CAN') {
                    //agregar al array solo si es diferente de cancelado
                    array_push($totales, $dataSocio);
                }
            }
        }
        $data = [
            'header' => $header,
            'totales' => $totales,
            'total' => array_sum(array_column($totales, 'monto')),
            'consumosMesFin' => $consumosMesFin
        ];

        $pdf = Pdf::loadView('reportes.cartera-vencida', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        return $pdf->stream('reporte-vencidos' . $mes_actual . '.pdf');
    }
}

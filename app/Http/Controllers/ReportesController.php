<?php

namespace App\Http\Controllers;

use App\Constants\AlmacenConstants;
use App\Exports\CarteraVencidaExport;
use App\Exports\EntradasExport;
use App\Exports\FacturasExport;
use App\Exports\RecibosExport;
use App\Exports\SociosExport;

use App\Exports\VentasExport;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\CatalogoVistaVerde;
use App\Models\DetalleEntradaNew;
use App\Models\DetallesCompra;
use App\Models\DetallesEntrada;
use App\Models\DetallesFacturas;
use App\Models\DetallesPeriodoNomina;
use App\Models\DetallesRequisicion;
use App\Models\DetallesVentaPago;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoCuenta;
use App\Models\Facturas;
use App\Models\Grupos;
use App\Models\Insumo;
use App\Models\OrdenCompra;
use App\Models\PeriodoNomina;
use App\Models\Presentacion;
use App\Models\Proveedor;
use App\Models\PuntoVenta;
use App\Models\Recibo;
use App\Models\Requisicion;
use App\Models\SaldoFavor;
use App\Models\Socio;
use App\Models\Stock;
use App\Models\TipoPago;
use App\Models\Unidad;
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

        $pdf = Pdf::loadView('reportes.nota-venta', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica']);
        //Tamaño predeterminado de papel del ticket (80mm x 297mm)
        $pdf->setPaper([0, 0, 226.772, 841.89], 'portrait');
        return $pdf->stream('venta.pdf');
    }

    //Genera reportes de ventas, con ayuda del corte de caja
    public function generarCorte(Caja $caja, $codigopv = null)
    {
        /*
        //Comprobamos si la caja no esta cerrada
        if (!$caja->fecha_cierre && $caja->clave_punto_venta != 'REC') {
            return redirect()->route('home');
        }
        */

        //Quitamos los metodos de pago no permitidos en una venta.
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

        //Obtenemos el total del corte, sin sumar el metodo de pago 'pendiente'
        $totalVenta = 0;
        foreach ($detalles_pago as $key => $pago) {
            $result = $tipos_pago->where('id', $pago->id_tipo_pago)->first();
            if ($result->descripcion != 'PENDIENTE')
                $totalVenta += $pago->monto;
        }

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
                ->orWhere('descripcion', 'like', '%SALDO%')
                ->orWhere('descripcion', 'like', '%PENDIENTE%');
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
                ->orWhere('descripcion', 'like', '%SALDO%')
                ->orWhere('descripcion', 'like', '%PENDIENTE%');
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
            'limite' => 'required'
        ]);
        //Agregamos el key para los consumos del mes
        $validated['consumosMesFin'] = $request->input('consumosMesFin', null);
        //Agregamos el key para indicar si agregar los socios cancelados o no
        $validated['cancelados'] = $request->input('cancelados', null);

        switch ($validated['typeFile']) {
            case 'XLS':
                $result = $this->vencidosExcel($validated['consumosMesFin'], $validated['cancelados'], $validated['limite']);
                break;
            case 'PDF':
                $result = $this->vencidosPdf($validated['consumosMesFin'], $validated['cancelados'], $validated['limite']);
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
                'ventas.observaciones',
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

    /**
     * LEGACY\
     * Genera el pdf de la requisiscion de compra (almacen)
     */
    public function generarRequisicion($folio, $order = false)
    {
        $requisicion = OrdenCompra::with('user')->find($folio);

        if ($order) {
            $detalle = DetallesCompra::where('folio_orden', $folio)
                ->orderBy('id_proveedor', 'ASC')
                ->orderBy('nombre', 'ASC')
                ->get()->toArray();
        } else {
            $detalle = DetallesCompra::where('folio_orden', $folio)
                ->get()->toArray();
        }
        //Generamos array, para busqueda indexada
        $proveedores = $this->generateIndex(Proveedor::all(), 'id', 'nombre');
        //Calcular el subtotal y el nuevo iva
        $subtotal = $this->calcularSubtotal($detalle);
        $iva = $this->calcularIva($detalle);

        $data = [
            'requisicion' => $requisicion->toArray(),
            'detalle' => $detalle,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'proveedores' => $proveedores,
        ];

        $pdf = Pdf::loadView('reportes.requisicion', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792], 'landscape'); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('requisicion' . $folio . '.pdf');
    }

    /**
     * Genera el pdf de la NUEVA requisicion
     */
    public function verRequi($folio, $order = false)
    {
        $requisicion = Requisicion::with('user')->find($folio);

        if ($order) {
            $detalle = DetallesRequisicion::where('folio_requisicion', $folio)
                ->orderBy('id_proveedor', 'ASC')
                ->orderBy('descripcion', 'ASC')
                ->get()->toArray();
        } else {
            $detalle = DetallesRequisicion::where('folio_requisicion', $folio)
                ->get()->toArray();
        }
        //Generamos array, para busqueda indexada
        $proveedores = $this->generateIndex(Proveedor::all(), 'id', 'nombre');
        //Calcular el subtotal y el nuevo iva
        $subtotal = array_sum(array_column($detalle, 'importe_sin_impuesto'));
        $iva = array_sum(array_column($detalle, 'impuesto'));

        $data = [
            'requisicion' => $requisicion,
            'detalle' => $detalle,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'proveedores' => $proveedores,
        ];

        $pdf = Pdf::loadView('reportes.requisicion-new', $data);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792], 'landscape'); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('requisicion' . $folio . '.pdf');
    }

    /**
     * Genera un reporte en Excel de los productos registrados en las entradas de almacen. Dado un rango de fechas y un proveedor
     */
    public function repEntradas(Request $request)
    {
        $fInicio = $request->input('fInicio');
        $fFin = $request->input('fFin');
        $id_proovedor = $request->input('proveedor');
        return Excel::download(
            new EntradasExport($fInicio, $fFin, $id_proovedor),
            ' Entradas ' . $fInicio . ' - ' . $fFin . '.xlsx'

        );
    }

    public function generarReporteExistencias(Request $request)
    {
        $lista = $request->input();
        //Remover el token csfr del formulario
        unset($lista['_token']);
        //Array auxiliar con la informacion de los articulos
        $data = [];

        foreach ($lista as $key => $codigo) {
            //Buscamos el articulo en el catalogo
            $articulo = CatalogoVistaVerde::with('stocks')
                ->where('codigo', $codigo)
                ->first();
            //Agregarlo al array de datos
            $data[] = [
                'codigo' => $codigo,
                'nombre' => $articulo->nombre,
                'stocks' => $articulo->stocks,
                'ultima_compra' => $articulo->ultima_compra
            ];
        }

        $pdf = Pdf::loadView('reportes.existencias', ['articulos' => $data]);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792], 'landscape'); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('existencias' . now()->toDateString() . '.pdf');
    }

    /**
     * Genera el pdf con todas las nominas a imprimir
     */
    public function imprimirNomina($ref)
    {
        //Buscar el registro del periodo de las nominas
        $periodo = PeriodoNomina::find($ref);
        //Buscar las nominas con ayuda de la referencia
        $nominas = DetallesPeriodoNomina::where('referencia_periodo', $ref)->get();
        //Crear el pdf
        $pdf = Pdf::loadView('reportes.Nominas.general', ['nominas' => $nominas, 'periodo' => $periodo]);
        $pdf->setOption(['defaultFont' => 'Courier']);
        $pdf->setPaper([0, 0, 612.283, 792]); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('Nominas-' . $ref . '.pdf');
    }

    private function generateIndex($collection, $primary_key, $name)
    {
        $aux = [];
        foreach ($collection as $key => $value) {
            $aux[$value->$primary_key] = $value->$name;
        }
        return $aux;
    }

    /**
     * Realiza la exportacion de datos a excel con la biblioteca 'maatwebsite/excel'
     * Recibe los parametros de busqueda, Si incluye los consumos del mes, los socios cancelados y la fecha limite del reporte
     */
    public function vencidosExcel($consumosMesFin, $cancelados, $fecha_limite)
    {
        $hoy = now()->toDateString();
        //Devolvemos el excel
        return Excel::download(
            new CarteraVencidaExport($consumosMesFin, $cancelados, $fecha_limite),
            'Cartera Vencida ' . $hoy . '.xlsx'
        );
    }

    /**
     * Genera un pdf con la cartera vencida (resumen).
     * Recibe los parametros de busqueda, Si incluye los consumos del mes, los socios cancelados y la fecha limite del reporte
     */
    public function vencidosPdf($consumosMesFin, $cancelados, $fecha_limite)
    {
        $mes_limite = Carbon::parse($fecha_limite);
        $header = [
            'title' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET.FED.MEX-PUE KM252 SAN NICOLAS TETIZINTLA TEHUACÁN, PUEBLA CP.75710',
            'telefono' => '3745011'
        ];
        if ($consumosMesFin) {
            $estados = EstadoCuenta::where('saldo', '>', 0)
                ->whereDate('fecha', '<=', $fecha_limite)
                ->orderBy('id_socio', 'asc')
                ->get();
        } else {
            $estados = EstadoCuenta::where('saldo', '>', 0)
                ->whereDate('fecha', '<=', $fecha_limite)
                ->whereNot(function (Builder $query) use ($mes_limite) {
                    $query->whereMonth('fecha', $mes_limite->month)
                        ->whereYear('fecha', $mes_limite->year)
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
                if (!$socio)
                    continue;
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
        return $pdf->stream('reporte-vencidos' . now()->toDateString() . '.pdf');
    }

    /**
     * Devuelve la vista para generar el reporte de existencias.
     */
    public function getExistencias()
    {
        $grupos = Grupos::where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        $bodegas = Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
        //Establecer fecha inicial
        $fecha = now()->toDateString();
        //Establecer hora inicial
        $hora = now()->setHour(23)->setMinute(59)->toTimeString("minute");

        //Devolver la vista
        return view('almacen.Documentos.existencias', [
            'grupos' => $grupos,
            'bodegas' => $bodegas,
            'fecha' => $fecha,
            'hora' => $hora,
        ]);
    }

    /**
     * Genera el reporte de existencias de la peticion POST
     */
    public function postExistencias(Request $request)
    {
        //Obtener los parametros de la peticion post.
        $folio = $request->input('folio');
        $bodega = Bodega::find($request->input('clave_bodega'));
        $fecha = $request->input('fecha');
        $hora = $request->input('hora');
        $grupos = $request->input('selected_grupos');   //Array de los id de grupos seleccionados
        $view_path = '';    //ruta de la vista a cargar en el pdf
        $service = new InventarioService(); //Objeto para consultar existencias
        $result = [];       //Array auxiliar

        //Definir reglas de validacion inicial
        $rules = [
            'fecha' => ['required'],
            'hora' => ['required'],
            'selected_grupos' => ['required']
        ];
        //Si hay folio de requisicion
        if (!is_null($folio)) {
            //Retirar la regla 'selected_grupos'
            unset($rules['selected_grupos']);
        }
        //Validamos la peticion
        $validated = $request->validate($rules);

        //Obtenemos todas las bodegas de tipo interna
        $bodegas = Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();

        //Si no selecciono bodega
        if (is_null($bodega)) {
            //Cambiar la ruta de la vista
            $view_path = 'reportes.existencias.existencias-todos';
            //Obtener el array inicial con los insumos y las columnas de las bodegas
            $result = $service->obtenerTodosInsumos($grupos, $bodegas, $folio);

            //Para cada bodega
            foreach ($bodegas as $b) {
                $temp = []; //Limipar el array temporal (que contiene las existencias de 1 sola bodega)
                //Si hay un folio de requi
                if (!is_null($folio)) {
                    //Buscar las existencias por clave de insumo y clave de bodega
                    foreach ($result as $key => $insum) {
                        $temp = array_merge(
                            $temp,
                            $service->existenciasInsumo($insum['clave'], $fecha, $hora, $b->clave)
                        );
                    }
                } else {
                    //Buscar las existencias por clave de grupo y clave de bodega
                    foreach ($grupos as $grupo_id) {
                        //Unir los resultados de cada consulta
                        $temp = array_merge(
                            $temp,
                            $service->consultarInsumos(Grupos::find($grupo_id), $fecha, $hora, $b->clave)
                        );
                    }
                }
                //Para todas las existencias obtenidas (almacenadas en el array temporal).
                foreach ($temp as $i => $row_insumo) {
                    //Actualizar las existencias de cada insumo indexado. segun la bodega en turno de la iteracion.
                    $result[$row_insumo['clave']][$b->clave] = $row_insumo['existencias_insumo'];
                }
            }
        } else {
            //Si hay un folio de requi
            if (!is_null($folio)) {
                //Buscar las existencias de la requisicion
                $result = $service->obtenerExistenciasRequi($folio, $fecha, $hora, $bodega->clave, $bodega->naturaleza);
            } else {
                if ($bodega->naturaleza == AlmacenConstants::INSUMOS_KEY) {
                    //Buscar las existencias por clave de grupo y clave de bodega
                    foreach ($grupos as $grupo_id)
                        $result = array_merge(
                            $result,
                            $service->consultarInsumos(Grupos::find($grupo_id), $fecha, $hora, $bodega->clave)
                        );
                } else {
                    //Buscar las existencias por clave de grupo y clave de bodega
                    foreach ($grupos as $grupo_id)
                        $result = array_merge(
                            $result,
                            $service->consultarPresentaciones(Grupos::find($grupo_id), $fecha, $hora, $bodega->clave)
                        );
                }
            }
            $view_path = $service->getView($bodega->naturaleza);
        }

        //Cargar la vista del reporte
        $pdf = Pdf::loadView($view_path, [
            'articulos' => $result,
            'fecha' => $fecha,
            'hora' => $hora,
            'bodega' => $bodega,
            'bodegas' => $bodegas,
        ]);
        //Definir fuente 
        $pdf->setOption(['defaultFont' => 'Courier']);
        //Definir orientacion y tamaño
        $pdf->setPaper([0, 0, 612.283, 792], 'landscape'); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('existencias' . now()->toDateString() . '.pdf');
    }

    /**
     * Devuelve la vista necesaria para consultar la tabla de inventarios
     */
    public function getInvSemanal()
    {
        $grupos = Grupos::where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        $bodegas = Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
        //Establecer fecha inicial
        $fecha = now()->toDateString();

        //Devolver la vista
        return view('almacen.Documentos.tabla-inventarios', [
            'grupos' => $grupos,
            'bodegas' => $bodegas,
            'fecha' => $fecha,
        ]);
    }

    /**
     * Genera el pdf de la semana correspondiente, a la tabla de inventarios
     */
    public function postInvSemanal(Request $request)
    {
        //Datos del request
        $fecha = $request->input('fecha');
        $grupos = $request->input('selected_grupos');   //Array de los id de grupos seleccionados
        $bodega = Bodega::find($request->input('clave_bodega'));

        //Validamos
        $validated = $request->validate([
            'fecha' => ['required'],
            'selected_grupos' => ['required']
        ]);

        //Crear el array de fechas (hasta 6 dias despues)
        $fechas = [];
        for ($i = 0; $i <= 5; $i++) {
            $fechas[] = Carbon::parse($fecha)->addDays($i);
        }

        if ($bodega->naturaleza == AlmacenConstants::INSUMOS_KEY) {
            //Obtener los Insumos
            $result  = Insumo::where('inventariable', 1)
                ->whereIn('id_grupo', $grupos)
                ->orderBy('descripcion')
                ->get();
        } else {
            //Obtener las presentaciones
            $result = Presentacion::where('estado', 1)
                ->whereIn('id_grupo', $grupos)
                ->orderBy('descripcion')
                ->get();
        }

        //Cargar la vista del reporte
        $pdf = Pdf::loadView('reportes.tabla-inv-semanal', [
            'articulos' => $result,
            'fechas' => $fechas,
            'bodega' => $bodega,
        ]);

        //Definir fuente 
        $pdf->setOption(['defaultFont' => 'Courier']);
        //Definir orientacion y tamaño
        $pdf->setPaper([0, 0, 612.283, 792], 'portrait'); // Tamaño aproximado del US LETTER (216 x 279.4) mm
        return $pdf->stream('existencias' . now()->toDateString() . '.pdf');
    }

    /**
     * Prepara la view para el reporte de entradas (Nuevo)
     */
    public function verEntradas()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $f_inicio = now()->format("Y-m-d");
        $f_fin = now()->format("Y-m-d");
        //Devolver la vista
        return view('almacen.Documentos.entradas', [
            'proveedores' => $proveedores,
            'f_inicio' => $f_inicio,
            'f_fin' => $f_fin
        ]);
    }

    /**
     * Genera un excel para descargar las (NUEVAS) entradas correspondientes
     */
    public function obtenerEntradas(Request $req)
    {
        $proveedores = $req->input('selected_proveedores');
        $f_inicio = $req->input('f_inicio');
        $f_fin = $req->input('f_fin');

        //Consulta
        $result = DetalleEntradaNew::with(['proveedor', 'entrada'])
            ->whereHas('entrada', function ($query) use ($f_inicio, $f_fin) {
                $query->whereDate('fecha_existencias', '>=', $f_inicio)
                    ->whereDate('fecha_existencias', '<=', $f_fin);
            })
            ->whereIn('id_proveedor', $proveedores)
            ->get();
        //Devolvemos el excel
        return Excel::download(
            new EntradasExport($result->toArray()),
            'Entradas ' . $f_inicio . ' - ' . $f_fin . '.xlsx'
        );
    }

    /**
     * Prepara la view para el reporte de facturas (Nuevo)
     */
    public function verFacturas()
    {
        //Extraer los datos de la peticion
        $proveedores = Proveedor::orderBy('nombre')->get();
        $f_inicio = now()->format("Y-m-d");
        $f_fin = now()->format("Y-m-d");
        //Devolver la vista
        return view('almacen.Documentos.facturas', [
            'proveedores' => $proveedores,
            'f_inicio' => $f_inicio,
            'f_fin' => $f_fin
        ]);
    }

    /**
     * Genera un Excel con todos los detalles de las facturas
     */
    public function obtenerFacturas(Request $req)
    {
        //Extraer los datos de la peticion
        $proveedores = $req->input('selected_proveedores');
        $f_inicio = $req->input('f_inicio');
        $f_fin = $req->input('f_fin');
        //Preparar consulta
        $result = DetallesFacturas::with(['factura', 'presentacion'])
            ->whereHas('factura', function ($query) use ($f_inicio, $f_fin, $proveedores) {
                $query->whereDate('fecha_compra', '>=', $f_inicio)
                    ->whereDate('fecha_compra', '<=', $f_fin)
                    ->whereIn('id_proveedor', $proveedores);
            })
            ->get();
        //Devolvemos el excel
        return Excel::download(
            new FacturasExport($result->toArray()),
            'Facturas ' . $f_inicio . ' - ' . $f_fin . '.xlsx'
        );
    }

    /**
     * Recibe los detalles de la requisicion, y convierte las columnas de "almacen, bar, barra, caddie, cafeteria, cocina"
     * en JSON.
     * Unicamente convierte dichas columnas del array de entrada.
     */
    private function convertJsonColums(array $detalles_requisicion)
    {
        $aux = array_map(function ($item) {
            $item->almacen = json_decode($item->almacen);
            $item->bar = json_decode($item->bar);
            $item->barra = json_decode($item->barra);
            $item->caddie = json_decode($item->caddie);
            $item->cafeteria = json_decode($item->cafeteria);
            $item->cocina = json_decode($item->cocina);
            return $item;
        }, $detalles_requisicion);

        return $aux;
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * cantidad)
     */
    private function calcularSubtotal($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Si la presentacion contiene el atributo eliminado, omitir
            if (array_key_exists('deleted', $presentacion)) continue;
            //Mutiplicar y acumular el valor
            $acu += $presentacion['costo_unitario'] * $presentacion['cantidad'];
        }
        return round($acu, 2);
    }

    /**
     * Obtiene la sumatoria de (costo_unitario * (iva / 100)) * $cantidad'
     */
    private function calcularIva($presentaciones)
    {
        $acu = 0;
        foreach ($presentaciones as $presentacion) {
            //Si la presentacion contiene el atributo eliminado, omitir
            if (array_key_exists('deleted', $presentacion)) continue;
            //Mutiplicar y acumular el valor
            $acu += ($presentacion['costo_unitario'] * ($presentacion['iva'] / 100)) * $presentacion['cantidad'];
        }
        return round($acu, 2);
    }
}

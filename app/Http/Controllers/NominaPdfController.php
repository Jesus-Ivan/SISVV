<?php

namespace App\Http\Controllers;

use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaRecibo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Luecano\NumeroALetras\NumeroALetras;

class NominaPdfController extends Controller
{
    /**
     * Genera el PDF de un recibo individual en tamaño media carta
     */
    public function reciboIndividual(int $id)
    {
        $recibo = NominaRecibo::with(['empleado.area', 'empleado.puesto', 'periodo'])->findOrFail($id);

        $formatter = new NumeroALetras();
        $totalLetras = $formatter->toMoney((float) $recibo->neto_pagar, 2, 'PESOS', 'CENTAVOS');

        $empresa = [
            'nombre' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET. FED. MEX-PUE KM 252, SAN NICOLAS TETIZINTLA, TEHUACÁN, PUEBLA CP. 75710',
            'telefono' => '(238) 374-5011',
        ];

        $data = [
            'empresa' => $empresa,
            'recibo' => $recibo,
            'totalLetras' => $totalLetras,
        ];

        $pdf = Pdf::loadView('reportes.Nominas.recibo-media-carta', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica', 'isHtml5ParserEnabled' => true]);
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Recibo-{$recibo->folio}.pdf");
    }

    /**
     * Genera un PDF consolidado a partir de una lista de recibos seleccionados
     * Reutilizando el formato base individual tamaño media carta para cada recibo
     */
    public function recibosMultiples(Request $request)
    {
        $idsParam = $request->input('ids');
        if (!$idsParam) {
            abort(400, 'No se proporcionaron recibos para imprimir');
        }

        $ids = is_array($idsParam) ? $idsParam : explode(',', $idsParam);
        $recibos = NominaRecibo::with(['empleado.area', 'empleado.puesto', 'periodo'])
            ->whereIn('id', $ids)
            ->orderBy('id_empleado_snapshot', 'asc')
            ->get();

        if ($recibos->isEmpty()) {
            abort(404, 'No se encontraron los recibos solicitados');
        }

        $formatter = new NumeroALetras();
        $recibosConLetras = $recibos->map(function ($recibo) use ($formatter) {
            $recibo->total_letras = $formatter->toMoney((float) $recibo->neto_pagar, 2, 'PESOS', 'CENTAVOS');
            return $recibo;
        });

        $empresa = [
            'nombre' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET. FED. MEX-PUE KM 252, SAN NICOLAS TETIZINTLA, TEHUACÁN, PUEBLA CP. 75710',
            'telefono' => '(238) 374-5011',
        ];

        $data = [
            'empresa' => $empresa,
            'recibos' => $recibosConLetras,
        ];

        $pdf = Pdf::loadView('reportes.Nominas.recibo-media-carta', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica', 'isHtml5ParserEnabled' => true]);
        
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Recibos-Nomina-Seleccion.pdf');
    }

    /**
     * Genera el PDF del comprobante / vale de caja para anticipos o abonos en efectivo
     */
    public function comprobanteMovimiento(int $id)
    {
        $movimiento = NominaMovimientoCuenta::with(['empleado.area', 'empleado.puesto'])->findOrFail($id);

        $formatter = new NumeroALetras();
        $totalLetras = $formatter->toMoney((float) $movimiento->monto, 2, 'PESOS', 'CENTAVOS');

        $empresa = [
            'nombre' => 'VISTA VERDE COUNTRY CLUB',
            'rfc' => 'VVC101110AQ4',
            'direccion' => 'CARRET. FED. MEX-PUE KM 252, SAN NICOLAS TETIZINTLA, TEHUACÁN, PUEBLA CP. 75710',
            'telefono' => '(238) 374-5011',
        ];

        $data = [
            'empresa' => $empresa,
            'movimiento' => $movimiento,
            'empleado' => $movimiento->empleado,
            'totalLetras' => $totalLetras,
        ];

        $pdf = Pdf::loadView('reportes.Nominas.comprobante-caja', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica', 'isHtml5ParserEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        $nombreArchivo = ($movimiento->folio ?: "MOV-{$movimiento->id}") . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Descarga plantilla Excel de nómina
     */
    public function descargarPlantilla(Request $request)
    {
        $idPeriodo = $request->input('id_periodo') ? (int) $request->input('id_periodo') : null;
        $soloIncompletos = $request->boolean('solo_incompletos');

        $periodo = $idPeriodo ? NominaPeriodo::find($idPeriodo) : null;
        if ($periodo && in_array(strtoupper($periodo->tipo_periodo), ['DIARIO', 'EXTRAORDINARIO'])) {
            $soloIncompletos = false;
        }

        if ($periodo && !empty($periodo->nombre)) {
            $nombrePeriodo = trim($periodo->nombre);
            // Reemplazar caracteres no permitidos en nombres de archivo
            $nombrePeriodo = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $nombrePeriodo);
        } else {
            $nombrePeriodo = $idPeriodo ? "Periodo-{$idPeriodo}" : 'Nomina';
        }

        $tipoPlantilla = $soloIncompletos ? 'Saldos Pendientes' : 'Plantilla Base';
        $fileName = "{$nombrePeriodo}-{$tipoPlantilla}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NominaPlantillaExport($idPeriodo, $soloIncompletos),
            $fileName
        );
    }

    /**
     * Exporta reporte de saldos y movimientos de empleados a Excel
     */
    public function exportarCuentasExcel(Request $request)
    {
        $idEmpleado = $request->input('id_empleado') ? (int) $request->input('id_empleado') : null;
        $tipo = $request->input('tipo_empleado');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $fileName = 'Cuentas_Saldos_Empleados_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NominaCuentasExport($idEmpleado, $tipo, $fechaInicio, $fechaFin),
            $fileName
        );
    }

    /**
     * Exporta reporte contable detallado de recibos de nómina a Excel
     */
    public function exportarReporteRecibos(Request $request)
    {
        $anio = $request->input('anio') ? (int) $request->input('anio') : null;
        $idPeriodo = $request->input('id_periodo') ? (int) $request->input('id_periodo') : null;
        $idEmpleado = $request->input('id_empleado') ? (int) $request->input('id_empleado') : null;
        $metodoPago = $request->input('metodo_pago');
        $estadoPago = $request->input('estado_pago');

        $fileName = 'Reporte_Recibos_Nomina_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NominaReporteRecibosExport($anio, $idPeriodo, $idEmpleado, $metodoPago, $estadoPago),
            $fileName
        );
    }
}

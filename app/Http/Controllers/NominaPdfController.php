<?php

namespace App\Http\Controllers;

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
        // Tamaño Media Carta (5.5 x 8.5 in): [0, 0, 396, 612]
        $pdf->setPaper([0, 0, 396, 612], 'portrait');

        return $pdf->stream("Recibo-{$recibo->folio}.pdf");
    }

    /**
     * Genera un PDF consolidado a partir de una lista de recibos seleccionados
     * Acomodando exactamente 2 recibos por cada hoja tamaño carta
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

        $pdf = Pdf::loadView('reportes.Nominas.recibos-multiples', $data);
        $pdf->setOption(['defaultFont' => 'Helvetica', 'isHtml5ParserEnabled' => true]);
        // Hoja carta estándar: [0, 0, 612, 792] en portrait
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Recibos-Nomina-Seleccion.pdf');
    }

    /**
     * Descarga plantilla Excel de nómina
     */
    public function descargarPlantilla(Request $request)
    {
        $idPeriodo = $request->input('id_periodo') ? (int) $request->input('id_periodo') : null;
        $fileName = 'Plantilla_Nomina_' . ($idPeriodo ? "Periodo_{$idPeriodo}_" : '') . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NominaPlantillaExport($idPeriodo),
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
}


<?php

namespace App\Imports;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaRecibo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NominaPlantillaImport implements ToCollection, WithHeadingRow
{
    public int $id_periodo;
    public int $procesados = 0;
    public array $errors = [];

    public function __construct(int $id_periodo)
    {
        $this->id_periodo = $id_periodo;
    }

    public function collection(Collection $rows)
    {
        $periodo = NominaPeriodo::findOrFail($this->id_periodo);

        DB::transaction(function () use ($rows, $periodo) {
            $consecutivosAsignados = [];

            foreach ($rows as $index => $row) {
                // Compatible con id_empleado o id
                $idEmpleado = $row['id_empleado'] ?? $row['id'] ?? null;
                if (!$idEmpleado) {
                    continue;
                }

                $empleado = NominaEmpleado::with(['area', 'puesto'])->find($idEmpleado);
                if (!$empleado) {
                    $this->errors[] = "Fila " . ($index + 2) . ": Empleado con ID '{$idEmpleado}' no existe en el sistema.";
                    continue;
                }

                $nominaFiscal = (float) ($row['nomina_fiscal'] ?? 0);
                $diferenciaEfectivo = (float) ($row['diferencia_efectivo'] ?? 0);
                $horasExtras = (float) ($row['horas_extras'] ?? 0);
                $bonosOtros = (float) ($row['bonos_otros'] ?? $row['otros_bonos'] ?? 0);
                $descuentoAdelanto = (float) ($row['descuento_adelanto'] ?? $row['descuento_adelantos'] ?? 0);
                $descuentoInfonavit = (float) ($row['descuento_infonavit'] ?? $row['infonavit'] ?? 0);
                $otrosDescuentos = (float) ($row['otros_descuentos'] ?? 0);
                $observaciones = $row['observaciones'] ?? null;

                $totalPercepciones = $nominaFiscal + $diferenciaEfectivo + $horasExtras + $bonosOtros;
                $totalDeducciones = $descuentoAdelanto + $descuentoInfonavit + $otrosDescuentos;
                $netoPagar = max(0, $totalPercepciones - $totalDeducciones);

                $recibo = NominaRecibo::where('id_periodo', $periodo->id)
                    ->where('id_empleado', $empleado->id)
                    ->first();

                if ($recibo) {
                    $recibo->update([
                        'nomina_fiscal' => $nominaFiscal,
                        'diferencia_efectivo' => $diferenciaEfectivo,
                        'horas_extras' => $horasExtras,
                        'bonos_otros' => $bonosOtros,
                        'total_percepciones' => $totalPercepciones,
                        'descuento_adelanto' => $descuentoAdelanto,
                        'descuento_infonavit' => $descuentoInfonavit,
                        'otros_descuentos' => $otrosDescuentos,
                        'total_deducciones' => $totalDeducciones,
                        'neto_pagar' => $netoPagar,
                        'observaciones' => $observaciones,
                    ]);
                } else {
                    $folioData = NominaRecibo::generarSiguienteFolio($empleado->id, $consecutivosAsignados[$empleado->id] ?? []);
                    $consecutivosAsignados[$empleado->id][] = $folioData['consecutivo'];

                    $recibo = NominaRecibo::create([
                        'folio' => $folioData['folio'],
                        'consecutivo_empleado' => $folioData['consecutivo'],
                        'id_periodo' => $periodo->id,
                        'id_empleado' => $empleado->id,
                        'tipo_recibo' => 'NOMINA',
                        'id_empleado_snapshot' => $empleado->id,
                        'nombre_empleado_snapshot' => $empleado->nombre_completo,
                        'area_snapshot' => $empleado->area ? $empleado->area->nombre : null,
                        'puesto_snapshot' => $empleado->puesto ? $empleado->puesto->nombre : null,
                        'tipo_empleado_snapshot' => $empleado->tipo_empleado,
                        'fecha_inicio' => $periodo->fecha_inicio,
                        'fecha_fin' => $periodo->fecha_fin,
                        'fecha_pago' => $periodo->fecha_pago ?: now()->toDateString(),
                        'nomina_fiscal' => $nominaFiscal,
                        'diferencia_efectivo' => $diferenciaEfectivo,
                        'horas_extras' => $horasExtras,
                        'bonos_otros' => $bonosOtros,
                        'total_percepciones' => $totalPercepciones,
                        'descuento_adelanto' => $descuentoAdelanto,
                        'descuento_infonavit' => $descuentoInfonavit,
                        'otros_descuentos' => $otrosDescuentos,
                        'total_deducciones' => $totalDeducciones,
                        'neto_pagar' => $netoPagar,
                        'metodo_pago' => 'EFECTIVO',
                        'estado_pago' => 'PENDIENTE',
                        'observaciones' => $observaciones,
                        'id_usuario' => auth()->id(),
                    ]);
                }

                // Si se amortiza adelanto, registrar en movimientos de cuenta
                if ($descuentoAdelanto > 0) {
                    $saldoAnterior = (float) $empleado->saldo_adelantos;
                    $saldoNuevo = max(0, $saldoAnterior - $descuentoAdelanto);

                    NominaMovimientoCuenta::create([
                        'id_empleado' => $empleado->id,
                        'id_recibo' => $recibo->id,
                        'id_periodo' => $periodo->id,
                        'tipo_movimiento' => 'DESCUENTO_NOMINA',
                        'naturaleza' => 'ABONO',
                        'monto' => $descuentoAdelanto,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $saldoNuevo,
                        'fecha' => now()->toDateString(),
                        'referencia' => "Dcto. Nómina Folio {$recibo->folio}",
                        'id_usuario' => auth()->id(),
                    ]);

                    $empleado->update(['saldo_adelantos' => $saldoNuevo]);
                }

                $this->procesados++;
            }

            $periodo->recalcularTotales();
        });
    }

    public function getRegistradosCount(): int
    {
        return $this->procesados;
    }

    public function getErrors(): array
    {
        return $this->errors ?? [];
    }
}

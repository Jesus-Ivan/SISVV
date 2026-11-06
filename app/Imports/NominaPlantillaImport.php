<?php

namespace App\Imports;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaPeriodoEmpleado;
use App\Models\NominaRecibo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NominaPlantillaImport implements ToCollection, WithHeadingRow
{
    public int $id_periodo;
    public int $procesados = 0;
    public int $filasOmitidas = 0;
    public int $metasSincronizadas = 0;
    public int $recibosActualizadosAPagado = 0;
    public array $errors = [];

    public function __construct(int $id_periodo)
    {
        $this->id_periodo = $id_periodo;
    }

    public function collection(Collection $rows)
    {
        $periodo = NominaPeriodo::findOrFail($this->id_periodo);

        $tipoPeriodo = strtoupper(trim($periodo->tipo_periodo ?? ''));
        $tipoRecibo = match ($tipoPeriodo) {
            'AGUINALDO'      => 'AGUINALDO',
            'FINIQUITO'      => 'FINIQUITO',
            'EXTRAORDINARIO' => 'EXTRAORDINARIO',
            default          => 'NOMINA', // DIARIO, SEMANAL, QUINCENAL, MENSUAL
        };

        DB::transaction(function () use ($rows, $periodo, $tipoRecibo) {
            $consecutivosAsignados = [];

            foreach ($rows as $index => $row) {
                $filaNum = $index + 2;

                // Compatible con id_empleado o id
                $idEmpleado = $row['id_empleado'] ?? $row['id'] ?? null;
                if (!$idEmpleado) {
                    continue;
                }

                $empleado = NominaEmpleado::with(['area', 'puesto', 'historialLaboral'])->find($idEmpleado);
                if (!$empleado) {
                    $this->errors[] = "Fila {$filaNum}: Empleado con ID '{$idEmpleado}' no existe en el sistema.";
                    continue;
                }

                // Sanitizar y validar números no negativos
                $nominaFiscal = $this->sanitizarNumero($row['nomina_fiscal'] ?? 0, 'Nómina Fiscal', $filaNum, $empleado);
                $diferenciaEfectivo = $this->sanitizarNumero($row['diferencia_efectivo'] ?? 0, 'Diferencia Efectivo', $filaNum, $empleado);
                $horasExtras = $this->sanitizarNumero($row['horas_extras'] ?? 0, 'Horas Extras', $filaNum, $empleado);
                $bonosOtros = $this->sanitizarNumero($row['bonos_otros'] ?? $row['otros_bonos'] ?? 0, 'Bonos/Otros', $filaNum, $empleado);

                $descuentoAdelanto = $this->sanitizarNumero($row['descuento_adelanto'] ?? $row['descuento_adelantos'] ?? 0, 'Descuento Adelanto', $filaNum, $empleado);
                $descuentoInfonavit = $this->sanitizarNumero($row['descuento_infonavit'] ?? $row['infonavit'] ?? 0, 'Descuento Infonavit', $filaNum, $empleado);
                $retardoInasistencias = $this->sanitizarNumero($row['retardo_inasistencias'] ?? $row['retardo_o_inasistencias'] ?? $row['retardos_inasistencias'] ?? 0, 'Retardo/Inasistencias', $filaNum, $empleado);
                $otrosDescuentos = $this->sanitizarNumero($row['otros_descuentos'] ?? 0, 'Otros Descuentos', $filaNum, $empleado);
                $observaciones = isset($row['observaciones']) && trim((string)$row['observaciones']) !== '' ? trim((string)$row['observaciones']) : null;

                // Meta acordada del período (Columna MONTO_ACORDADO_TOTAL)
                $rawMonto = $row['monto_acordado_total'] ?? $row['monto_acordado'] ?? null;
                $tieneMetaExplicita = false;
                if ($rawMonto !== null && trim((string) $rawMonto) !== '' && is_numeric($rawMonto)) {
                    $montoAcordado = max(0, (float) $rawMonto);
                    NominaPeriodoEmpleado::updateOrCreate(
                        ['id_periodo' => $periodo->id, 'id_empleado' => $empleado->id],
                        ['monto_acordado' => $montoAcordado]
                    );
                    $tieneMetaExplicita = true;
                }

                $totalPercepciones = round($nominaFiscal + $diferenciaEfectivo + $horasExtras + $bonosOtros, 2);
                $totalDeduccionesFijas = round($descuentoInfonavit + $retardoInasistencias + $otrosDescuentos, 2);

                // Si la fila no contiene percepciones ni deducciones a emitir
                if ($totalPercepciones <= 0.0001 && $totalDeduccionesFijas <= 0.0001 && $descuentoAdelanto <= 0.0001) {
                    if ($tieneMetaExplicita) {
                        $this->metasSincronizadas++;
                    } else {
                        $this->filasOmitidas++;
                    }
                    continue; // No emitir recibo vacío de $0.00
                }

                // Alerta si el empleado está inactivo o fuera de su ciclo laboral
                $ciclo = $empleado->historialLaboral->first(function ($c) use ($periodo) {
                    return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
                });
                if (!$empleado->activo || !$ciclo) {
                    $motivo = !$empleado->activo ? 'se encuentra INACTIVO' : 'no tiene ciclo laboral activo en las fechas del período';
                    $this->errors[] = "Fila {$filaNum}: Empleado #{$idEmpleado} ({$empleado->nombre_completo}) {$motivo}. Se emitió el recibo bajo advertencia administrativa.";
                }

                // Validar deuda de adelantos disponible
                $cargos = (float) NominaMovimientoCuenta::where('id_empleado', $empleado->id)->where('naturaleza', 'CARGO')->sum('monto');
                $abonos = (float) NominaMovimientoCuenta::where('id_empleado', $empleado->id)->where('naturaleza', 'ABONO')->sum('monto');
                $deudaDisponible = max(0, round($cargos - $abonos, 2));

                // Tope de adelanto: no puede exceder la deuda ni el disponible líquido de las percepciones
                $disponibleParaAdelanto = max(0, round($totalPercepciones - $totalDeduccionesFijas, 2));
                $maximoAdelantoPermitido = min($deudaDisponible, $disponibleParaAdelanto);

                if ($descuentoAdelanto > $maximoAdelantoPermitido) {
                    if ($deudaDisponible <= 0.001) {
                        $this->errors[] = "Fila {$filaNum}: Empleado #{$idEmpleado} ({$empleado->nombre_completo}) no cuenta con saldo deudor disponible ($0.00). Se ajustó descuento de adelanto a $0.00.";
                        $descuentoAdelanto = 0;
                    } elseif ($descuentoAdelanto > $deudaDisponible) {
                        $this->errors[] = "Fila {$filaNum}: Empleado #{$idEmpleado} ({$empleado->nombre_completo}) solo adeuda $" . number_format($deudaDisponible, 2) . ". Se ajustó descuento a $" . number_format($maximoAdelantoPermitido, 2) . ".";
                        $descuentoAdelanto = $maximoAdelantoPermitido;
                    } elseif ($descuentoAdelanto > $disponibleParaAdelanto) {
                        $this->errors[] = "Fila {$filaNum}: Empleado #{$idEmpleado} ({$empleado->nombre_completo}) el descuento de adelanto ($" . number_format($descuentoAdelanto, 2) . ") supera sus percepciones netas disponibles ($" . number_format($disponibleParaAdelanto, 2) . "). Se ajustó a $" . number_format($disponibleParaAdelanto, 2) . " para evitar saldo negativo.";
                        $descuentoAdelanto = $disponibleParaAdelanto;
                    }
                }

                if ($totalDeduccionesFijas > $totalPercepciones) {
                    $deficit = round($totalDeduccionesFijas - $totalPercepciones, 2);
                    $this->errors[] = "Fila {$filaNum}: Empleado #{$idEmpleado} ({$empleado->nombre_completo}) sus deducciones fijas ($" . number_format($totalDeduccionesFijas, 2) . ") superan sus percepciones ($" . number_format($totalPercepciones, 2) . "). El recibo quedará con neto $0.00 y un déficit no retenido de $" . number_format($deficit, 2) . ".";
                }

                $totalDeducciones = round($descuentoAdelanto + $totalDeduccionesFijas, 2);
                $netoPagar = max(0, round($totalPercepciones - $totalDeducciones, 2));

                // Determinar Método de Pago
                if ($nominaFiscal > 0 && $diferenciaEfectivo == 0) {
                    $metodoPago = 'TRANSFERENCIA';
                } elseif ($diferenciaEfectivo > 0 && $nominaFiscal == 0) {
                    $metodoPago = 'EFECTIVO';
                } elseif ($nominaFiscal > 0 && $diferenciaEfectivo > 0) {
                    $metodoPago = 'MIXTO';
                } else {
                    $metodoPago = $empleado->tipo_pago_predeterminado ?: 'EFECTIVO';
                }

                // Consultar recibos previos del empleado en este período
                $recibosPrevios = NominaRecibo::where('id_periodo', $periodo->id)
                    ->where('id_empleado', $empleado->id)
                    ->get();

                // Actualizar recibos previos en PENDIENTE o PARCIAL a PAGADO
                foreach ($recibosPrevios as $previo) {
                    if (in_array($previo->estado_pago, ['PENDIENTE', 'PARCIAL'])) {
                        $previo->update([
                            'estado_pago' => 'PAGADO',
                            'fecha_pago_real' => $previo->fecha_pago_real ?: now()->toDateString(),
                        ]);
                        $this->recibosActualizadosAPagado++;
                    }
                }

                // Preparar observaciones del nuevo recibo
                if ($observaciones !== null) {
                    $observacionesFinal = $observaciones;
                } elseif ($recibosPrevios->isNotEmpty()) {
                    $numComplemento = $recibosPrevios->count() + 1;
                    $observacionesFinal = "Abono #{$numComplemento} - Saldo restante";
                } else {
                    $observacionesFinal = null;
                }

                // Generar consecutivo y folio propio del empleado
                $folioData = NominaRecibo::generarSiguienteFolio($empleado->id, $consecutivosAsignados[$empleado->id] ?? []);
                $consecutivosAsignados[$empleado->id][] = $folioData['consecutivo'];

                // SIEMPRE crear un nuevo recibo (no sobreescribir)
                $recibo = NominaRecibo::create([
                    'folio'                    => $folioData['folio'],
                    'consecutivo_empleado'     => $folioData['consecutivo'],
                    'id_periodo'               => $periodo->id,
                    'id_empleado'              => $empleado->id,
                    'tipo_recibo'              => $tipoRecibo,
                    'id_empleado_snapshot'     => $empleado->id,
                    'nombre_empleado_snapshot' => $empleado->nombre_completo,
                    'area_snapshot'            => $empleado->area ? $empleado->area->nombre : null,
                    'puesto_snapshot'          => $empleado->puesto ? $empleado->puesto->nombre : null,
                    'tipo_empleado_snapshot'   => $empleado->tipo_empleado,
                    'fecha_inicio'             => $periodo->fecha_inicio,
                    'fecha_fin'                => $periodo->fecha_fin,
                    'fecha_pago'               => $periodo->fecha_pago ?: now()->toDateString(),
                    'nomina_fiscal'            => $nominaFiscal,
                    'diferencia_efectivo'      => $diferenciaEfectivo,
                    'horas_extras'             => $horasExtras,
                    'bonos_otros'              => $bonosOtros,
                    'total_percepciones'       => $totalPercepciones,
                    'descuento_adelanto'       => $descuentoAdelanto,
                    'descuento_infonavit'      => $descuentoInfonavit,
                    'retardo_inasistencias'    => $retardoInasistencias,
                    'otros_descuentos'         => $otrosDescuentos,
                    'total_deducciones'        => $totalDeducciones,
                    'neto_pagar'               => $netoPagar,
                    'metodo_pago'              => $metodoPago,
                    'estado_pago'              => 'PENDIENTE',
                    'observaciones'            => $observacionesFinal,
                    'id_usuario'               => auth()->id(),
                ]);

                // Registrar amortización de adelanto en el kardex contable
                if ($descuentoAdelanto > 0) {
                    $saldoAnterior = (float) $empleado->saldo_adelantos;
                    $saldoNuevo = max(0, round($saldoAnterior - $descuentoAdelanto, 2));
                    $folioMov = NominaMovimientoCuenta::generarSiguienteFolio($empleado->id, 'DESCUENTO_NOMINA');

                    NominaMovimientoCuenta::create([
                        'id_empleado'          => $empleado->id,
                        'id_recibo'            => $recibo->id,
                        'id_periodo'           => $periodo->id,
                        'folio'                => $folioMov['folio'],
                        'consecutivo_empleado' => $folioMov['consecutivo'],
                        'tipo_movimiento'      => 'DESCUENTO_NOMINA',
                        'naturaleza'           => 'ABONO',
                        'monto'                => $descuentoAdelanto,
                        'saldo_anterior'       => $saldoAnterior,
                        'saldo_nuevo'          => $saldoNuevo,
                        'fecha'                => now()->toDateString(),
                        'referencia'           => "Dcto. Nómina Folio {$recibo->folio}",
                        'id_usuario'           => auth()->id(),
                    ]);

                    $empleado->reconciliarKardexSaldos();
                }

                $this->procesados++;
            }

            $periodo->recalcularTotales();
        });
    }

    private function sanitizarNumero(mixed $valor, string $campo, int $fila, NominaEmpleado $empleado): float
    {
        $num = (float) ($valor ?? 0);
        if ($num < 0) {
            $this->errors[] = "Fila {$fila}: Empleado #{$empleado->id} ({$empleado->nombre_completo}) contenía un valor negativo en '{$campo}' ($" . number_format($num, 2) . "). Se ajustó a $0.00.";
            return 0.0;
        }
        return round($num, 2);
    }

    public function getRegistradosCount(): int
    {
        return $this->procesados;
    }

    public function getOmitidosCount(): int
    {
        return $this->filasOmitidas;
    }

    public function getMetasSincronizadasCount(): int
    {
        return $this->metasSincronizadas;
    }

    public function getRecibosActualizadosAPagadoCount(): int
    {
        return $this->recibosActualizadosAPagado;
    }

    public function getErrors(): array
    {
        return $this->errors ?? [];
    }
}

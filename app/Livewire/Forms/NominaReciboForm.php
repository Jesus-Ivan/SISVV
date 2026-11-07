<?php

namespace App\Livewire\Forms;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaPeriodoEmpleado;
use App\Models\NominaRecibo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NominaReciboForm extends Form
{
    public ?NominaRecibo $recibo = null;

    #[Validate('nullable|exists:nomina_periodos,id')]
    public int|string|null $id_periodo = null;

    #[Validate('required|exists:nomina_empleados,id')]
    public int|string|null $id_empleado = null;

    #[Validate('required|in:NOMINA,AGUINALDO,FINIQUITO,EXTRAORDINARIO')]
    public string $tipo_recibo = 'NOMINA';

    #[Validate('required|date')]
    public string $fecha_inicio = '';

    #[Validate('required|date|after_or_equal:fecha_inicio')]
    public string $fecha_fin = '';

    #[Validate('nullable|date')]
    public ?string $fecha_pago = null;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $nomina_fiscal = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $diferencia_efectivo = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $horas_extras = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $bonos_otros = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $descuento_adelanto = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $descuento_infonavit = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $retardo_inasistencias = 0;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $otros_descuentos = 0;

    #[Validate('required|in:EFECTIVO,TRANSFERENCIA,MIXTO')]
    public string $metodo_pago = 'EFECTIVO';

    #[Validate('required|in:PAGADO,PENDIENTE,PARCIAL')]
    public string $estado_pago = 'PENDIENTE';

    #[Validate('nullable|date')]
    public ?string $fecha_pago_real = null;

    #[Validate('nullable|string')]
    public ?string $observaciones = null;

    #[Validate('nullable|numeric|min:0')]
    public float|int|string|null $monto_total_acordado = null;

    public function setRecibo(NominaRecibo $recibo): void
    {
        $this->recibo = $recibo;
        $this->id_periodo = $recibo->id_periodo;
        $this->id_empleado = $recibo->id_empleado;
        $this->tipo_recibo = $recibo->tipo_recibo;
        $this->fecha_inicio = $recibo->fecha_inicio ? $recibo->fecha_inicio->format('Y-m-d') : '';
        $this->fecha_fin = $recibo->fecha_fin ? $recibo->fecha_fin->format('Y-m-d') : '';
        $this->fecha_pago = $recibo->fecha_pago ? $recibo->fecha_pago->format('Y-m-d') : null;
        $this->nomina_fiscal = (float) $recibo->nomina_fiscal;
        $this->diferencia_efectivo = (float) $recibo->diferencia_efectivo;
        $this->horas_extras = (float) $recibo->horas_extras;
        $this->bonos_otros = (float) $recibo->bonos_otros;
        $this->descuento_adelanto = (float) $recibo->descuento_adelanto;
        $this->descuento_infonavit = (float) $recibo->descuento_infonavit;
        $this->retardo_inasistencias = (float) $recibo->retardo_inasistencias;
        $this->otros_descuentos = (float) $recibo->otros_descuentos;
        $this->metodo_pago = $recibo->metodo_pago;
        $this->estado_pago = $recibo->estado_pago;
        $this->fecha_pago_real = $recibo->fecha_pago_real ? $recibo->fecha_pago_real->format('Y-m-d') : null;
        $this->observaciones = $recibo->observaciones;

        $acordado = NominaPeriodoEmpleado::where('id_periodo', $recibo->id_periodo)
            ->where('id_empleado', $recibo->id_empleado)
            ->value('monto_acordado');
        $this->monto_total_acordado = $acordado !== null ? (float) $acordado : null;
    }

    public function calcularPercepciones(): float
    {
        return (float) (
            (float) ($this->nomina_fiscal ?? 0) +
            (float) ($this->diferencia_efectivo ?? 0) +
            (float) ($this->horas_extras ?? 0) +
            (float) ($this->bonos_otros ?? 0)
        );
    }

    public function calcularDeducciones(): float
    {
        return (float) (
            (float) ($this->descuento_adelanto ?? 0) +
            (float) ($this->descuento_infonavit ?? 0) +
            (float) ($this->retardo_inasistencias ?? 0) +
            (float) ($this->otros_descuentos ?? 0)
        );
    }

    public function calcularNeto(): float
    {
        return max(0, $this->calcularPercepciones() - $this->calcularDeducciones());
    }

    /**
     * Calcula la deuda de adelantos máxima amortizable para este recibo
     */
    public function calcularDeudaDisponible(?NominaEmpleado $empleado = null): float
    {
        $empId = $empleado ? $empleado->id : $this->id_empleado;
        if (!$empId) {
            return 0.0;
        }

        $cargos = (float) NominaMovimientoCuenta::where('id_empleado', $empId)
            ->where('naturaleza', 'CARGO')
            ->sum('monto');

        $queryAbonos = NominaMovimientoCuenta::where('id_empleado', $empId)
            ->where('naturaleza', 'ABONO');

        // Si estamos editando un recibo existente, excluimos el abono de este mismo recibo
        if ($this->recibo && $this->recibo->id) {
            $queryAbonos->where(function ($q) {
                $q->whereNull('id_recibo')->orWhere('id_recibo', '!=', $this->recibo->id);
            });
        }

        $abonos = (float) $queryAbonos->sum('monto');

        return max(0, round($cargos - $abonos, 2));
    }

    /**
     * Valida que la amortización de adelanto no exceda la deuda real disponible
     */
    public function validateDeudaAdelanto(): void
    {
        $descuento = (float) ($this->descuento_adelanto ?? 0);
        if ($descuento > 0) {
            $deudaDisponible = $this->calcularDeudaDisponible();
            if ($deudaDisponible <= 0.001) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'form.descuento_adelanto' => 'El empleado no cuenta con saldo deudor de adelantos por amortizar ($0.00).',
                ]);
            } elseif ($descuento > $deudaDisponible + 0.001) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'form.descuento_adelanto' => 'La amortización ($' . number_format($descuento, 2) . ') no puede superar el saldo deudor disponible ($' . number_format($deudaDisponible, 2) . ').',
                ]);
            }

            // Validar que el descuento de adelanto no supere las percepciones líquidas disponibles del recibo
            $percepciones = $this->calcularPercepciones();
            $deduccionesFijas = (float) ($this->descuento_infonavit ?? 0)
                + (float) ($this->retardo_inasistencias ?? 0)
                + (float) ($this->otros_descuentos ?? 0);
            $disponibleParaAdelanto = max(0, round($percepciones - $deduccionesFijas, 2));

            if ($descuento > $disponibleParaAdelanto + 0.001) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'form.descuento_adelanto' => 'El descuento de adelanto ($' . number_format($descuento, 2) . ') no puede superar las percepciones disponibles del recibo ($' . number_format($disponibleParaAdelanto, 2) . ').',
                ]);
            }
        }
    }

    public function store(): NominaRecibo
    {
        $this->validate();
        $this->validateDeudaAdelanto();

        $totalPercepciones = $this->calcularPercepciones();
        if ($totalPercepciones <= 0.001) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.nomina_fiscal' => 'No se puede emitir un recibo con percepciones totales en $0.00. Ingresa sueldo fiscal o diferencia en efectivo.',
            ]);
        }

        // Auto-asignación inteligente del método de pago
        $fiscal = (float) ($this->nomina_fiscal ?? 0);
        $efectivo = (float) ($this->diferencia_efectivo ?? 0);
        if ($fiscal > 0 && $efectivo > 0) {
            $this->metodo_pago = 'MIXTO';
        } elseif ($fiscal > 0 && $efectivo == 0) {
            $this->metodo_pago = 'TRANSFERENCIA';
        } elseif ($efectivo > 0 && $fiscal == 0) {
            $this->metodo_pago = 'EFECTIVO';
        }

        $empleado = NominaEmpleado::with('area', 'puesto')->findOrFail($this->id_empleado);

        return DB::transaction(function () use ($empleado) {
            // Transicionar recibos previos en PENDIENTE o PARCIAL a PAGADO al emitir un nuevo abono
            if ($this->id_periodo) {
                NominaRecibo::where('id_periodo', $this->id_periodo)
                    ->where('id_empleado', $empleado->id)
                    ->whereIn('estado_pago', ['PENDIENTE', 'PARCIAL'])
                    ->update([
                        'estado_pago' => 'PAGADO',
                        'fecha_pago_real' => now()->toDateString(),
                    ]);
            }

            $folioData = NominaRecibo::generarSiguienteFolio($empleado->id);
            $totalPercepciones = $this->calcularPercepciones();
            $totalDeducciones = $this->calcularDeducciones();
            $neto = $this->calcularNeto();

            $descuentoAdelanto = (float) ($this->descuento_adelanto ?? 0);

            $recibo = NominaRecibo::create([
                'folio' => $folioData['folio'],
                'consecutivo_empleado' => $folioData['consecutivo'],
                'id_periodo' => $this->id_periodo,
                'id_empleado' => $empleado->id,
                'tipo_recibo' => $this->tipo_recibo,
                'id_empleado_snapshot' => $empleado->id,
                'nombre_empleado_snapshot' => $empleado->nombre_completo,
                'area_snapshot' => $empleado->area ? $empleado->area->nombre : null,
                'puesto_snapshot' => $empleado->puesto ? $empleado->puesto->nombre : null,
                'tipo_empleado_snapshot' => $empleado->tipo_empleado,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'fecha_pago' => $this->fecha_pago ?: now()->toDateString(),
                'nomina_fiscal' => (float) ($this->nomina_fiscal ?? 0),
                'diferencia_efectivo' => (float) ($this->diferencia_efectivo ?? 0),
                'horas_extras' => (float) ($this->horas_extras ?? 0),
                'bonos_otros' => (float) ($this->bonos_otros ?? 0),
                'total_percepciones' => $totalPercepciones,
                'descuento_adelanto' => $descuentoAdelanto,
                'descuento_infonavit' => (float) ($this->descuento_infonavit ?? 0),
                'retardo_inasistencias' => (float) ($this->retardo_inasistencias ?? 0),
                'otros_descuentos' => (float) ($this->otros_descuentos ?? 0),
                'total_deducciones' => $totalDeducciones,
                'neto_pagar' => $neto,
                'metodo_pago' => $this->metodo_pago,
                'estado_pago' => $this->estado_pago,
                'fecha_pago_real' => $this->estado_pago === 'PAGADO' ? ($this->fecha_pago_real ?: now()->toDateString()) : null,
                'observaciones' => $this->observaciones,
                'id_usuario' => auth()->id(),
            ]);

            // Si hay descuento de adelanto, registrar abono a la cuenta contable del empleado
            if ($descuentoAdelanto > 0) {
                $saldoAnterior = (float) $empleado->saldo_adelantos;
                $saldoNuevo = max(0, round($saldoAnterior - $descuentoAdelanto, 2));
                $folioData = NominaMovimientoCuenta::generarSiguienteFolio($empleado->id, 'DESCUENTO_NOMINA');

                NominaMovimientoCuenta::create([
                    'id_empleado' => $empleado->id,
                    'id_recibo' => $recibo->id,
                    'id_periodo' => $this->id_periodo,
                    'folio' => $folioData['folio'],
                    'consecutivo_empleado' => $folioData['consecutivo'],
                    'tipo_movimiento' => 'DESCUENTO_NOMINA',
                    'naturaleza' => 'ABONO',
                    'monto' => $descuentoAdelanto,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo' => $saldoNuevo,
                    'fecha' => now()->toDateString(),
                    'referencia' => "Descuento en recibo {$recibo->folio}",
                    'id_usuario' => auth()->id(),
                ]);

                $empleado->reconciliarKardexSaldos();
            }

            if ($this->id_periodo && $this->monto_total_acordado !== null && $this->monto_total_acordado !== '') {
                NominaPeriodoEmpleado::updateOrCreate(
                    ['id_periodo' => $this->id_periodo, 'id_empleado' => $empleado->id],
                    ['monto_acordado' => (float) $this->monto_total_acordado]
                );
            }

            if ($this->id_periodo) {
                NominaPeriodo::find($this->id_periodo)?->recalcularTotales();
            }

            return $recibo;
        });
    }

    public function update(): void
    {
        if (!$this->recibo) {
            return;
        }

        $this->validate();
        $this->validateDeudaAdelanto();

        $totalPercepciones = $this->calcularPercepciones();
        if ($totalPercepciones <= 0.001) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.nomina_fiscal' => 'No se puede guardar un recibo con percepciones totales en $0.00. Ingresa sueldo fiscal o diferencia en efectivo.',
            ]);
        }

        // Auto-asignación inteligente del método de pago
        $fiscal = (float) ($this->nomina_fiscal ?? 0);
        $efectivo = (float) ($this->diferencia_efectivo ?? 0);
        if ($fiscal > 0 && $efectivo > 0) {
            $this->metodo_pago = 'MIXTO';
        } elseif ($fiscal > 0 && $efectivo == 0) {
            $this->metodo_pago = 'TRANSFERENCIA';
        } elseif ($efectivo > 0 && $fiscal == 0) {
            $this->metodo_pago = 'EFECTIVO';
        }

        DB::transaction(function () {
            $totalPercepciones = $this->calcularPercepciones();
            $totalDeducciones = $this->calcularDeducciones();
            $neto = $this->calcularNeto();

            $viejoDescuento = (float) ($this->recibo->descuento_adelanto ?? 0);
            $nuevoDescuento = (float) ($this->descuento_adelanto ?? 0);
            $delta = round($nuevoDescuento - $viejoDescuento, 2);

            $this->recibo->update([
                'tipo_recibo' => $this->tipo_recibo,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'fecha_pago' => $this->fecha_pago ?: null,
                'nomina_fiscal' => (float) ($this->nomina_fiscal ?? 0),
                'diferencia_efectivo' => (float) ($this->diferencia_efectivo ?? 0),
                'horas_extras' => (float) ($this->horas_extras ?? 0),
                'bonos_otros' => (float) ($this->bonos_otros ?? 0),
                'total_percepciones' => $totalPercepciones,
                'descuento_adelanto' => $nuevoDescuento,
                'descuento_infonavit' => (float) ($this->descuento_infonavit ?? 0),
                'retardo_inasistencias' => (float) ($this->retardo_inasistencias ?? 0),
                'otros_descuentos' => (float) ($this->otros_descuentos ?? 0),
                'total_deducciones' => $totalDeducciones,
                'neto_pagar' => $neto,
                'metodo_pago' => $this->metodo_pago,
                'estado_pago' => $this->estado_pago,
                'fecha_pago_real' => $this->estado_pago === 'PAGADO' ? ($this->fecha_pago_real ?: now()->toDateString()) : null,
                'observaciones' => $this->observaciones,
            ]);

            // Sincronizar saldo de adelantos del empleado y movimientos contables si cambió el descuento
            if ($delta != 0) {
                $empleado = NominaEmpleado::lockForUpdate()->find($this->recibo->id_empleado);
                if ($empleado) {
                    $movimiento = NominaMovimientoCuenta::where('id_recibo', $this->recibo->id)
                        ->where('tipo_movimiento', 'DESCUENTO_NOMINA')
                        ->first();

                    if ($movimiento) {
                        if ($nuevoDescuento > 0) {
                            $movimiento->update([
                                'monto' => $nuevoDescuento,
                                'referencia' => "Descuento en recibo {$this->recibo->folio}",
                            ]);
                        } else {
                            $movimiento->delete();
                        }
                    } elseif ($nuevoDescuento > 0) {
                        $folioData = NominaMovimientoCuenta::generarSiguienteFolio($empleado->id, 'DESCUENTO_NOMINA');
                        NominaMovimientoCuenta::create([
                            'id_empleado' => $empleado->id,
                            'id_recibo' => $this->recibo->id,
                            'id_periodo' => $this->recibo->id_periodo,
                            'folio' => $folioData['folio'],
                            'consecutivo_empleado' => $folioData['consecutivo'],
                            'tipo_movimiento' => 'DESCUENTO_NOMINA',
                            'naturaleza' => 'ABONO',
                            'monto' => $nuevoDescuento,
                            'saldo_anterior' => (float) $empleado->saldo_adelantos,
                            'saldo_nuevo' => max(0, (float) $empleado->saldo_adelantos - $nuevoDescuento),
                            'fecha' => now()->toDateString(),
                            'referencia' => "Descuento en recibo {$this->recibo->folio}",
                            'id_usuario' => auth()->id(),
                        ]);
                    }

                    // Recalcular y reconciliar siempre el kardex en cascada para el empleado
                    $empleado->reconciliarKardexSaldos();
                }
            }

            if ($this->recibo->id_periodo && $this->monto_total_acordado !== null && $this->monto_total_acordado !== '') {
                NominaPeriodoEmpleado::updateOrCreate(
                    ['id_periodo' => $this->recibo->id_periodo, 'id_empleado' => $this->recibo->id_empleado],
                    ['monto_acordado' => (float) $this->monto_total_acordado]
                );
            }

            if ($this->recibo->id_periodo) {
                NominaPeriodo::find($this->recibo->id_periodo)?->recalcularTotales();
            }
        });
    }
}


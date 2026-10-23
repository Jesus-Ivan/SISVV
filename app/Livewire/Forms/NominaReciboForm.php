<?php

namespace App\Livewire\Forms;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use App\Models\NominaPeriodo;
use App\Models\NominaRecibo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NominaReciboForm extends Form
{
    public ?NominaRecibo $recibo = null;

    #[Validate('nullable|exists:nomina_periodos,id')]
    public ?int $id_periodo = null;

    #[Validate('required|exists:nomina_empleados,id')]
    public ?int $id_empleado = null;

    #[Validate('required|in:NOMINA,AGUINALDO,FINIQUITO,EXTRAORDINARIO')]
    public string $tipo_recibo = 'NOMINA';

    #[Validate('required|date')]
    public string $fecha_inicio = '';

    #[Validate('required|date|after_or_equal:fecha_inicio')]
    public string $fecha_fin = '';

    #[Validate('nullable|date')]
    public ?string $fecha_pago = null;

    #[Validate('required|numeric|min:0')]
    public float $nomina_fiscal = 0;

    #[Validate('required|numeric|min:0')]
    public float $diferencia_efectivo = 0;

    #[Validate('required|numeric|min:0')]
    public float $horas_extras = 0;

    #[Validate('required|numeric|min:0')]
    public float $bonos_otros = 0;

    #[Validate('required|numeric|min:0')]
    public float $monto_aguinaldo = 0;

    #[Validate('required|numeric|min:0')]
    public float $monto_vacaciones = 0;

    #[Validate('required|numeric|min:0')]
    public float $monto_prima_vacacional = 0;

    #[Validate('required|numeric|min:0')]
    public float $descuento_adelanto = 0;

    #[Validate('required|numeric|min:0')]
    public float $descuento_infonavit = 0;

    #[Validate('required|numeric|min:0')]
    public float $otros_descuentos = 0;

    #[Validate('required|in:EFECTIVO,TRANSFERENCIA,MIXTO')]
    public string $metodo_pago = 'EFECTIVO';

    #[Validate('required|in:PAGADO,PENDIENTE,PARCIAL')]
    public string $estado_pago = 'PENDIENTE';

    #[Validate('nullable|date')]
    public ?string $fecha_pago_real = null;

    #[Validate('nullable|string')]
    public ?string $observaciones = null;

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
        $this->monto_aguinaldo = (float) $recibo->monto_aguinaldo;
        $this->monto_vacaciones = (float) $recibo->monto_vacaciones;
        $this->monto_prima_vacacional = (float) $recibo->monto_prima_vacacional;
        $this->descuento_adelanto = (float) $recibo->descuento_adelanto;
        $this->descuento_infonavit = (float) $recibo->descuento_infonavit;
        $this->otros_descuentos = (float) $recibo->otros_descuentos;
        $this->metodo_pago = $recibo->metodo_pago;
        $this->estado_pago = $recibo->estado_pago;
        $this->fecha_pago_real = $recibo->fecha_pago_real ? $recibo->fecha_pago_real->format('Y-m-d') : null;
        $this->observaciones = $recibo->observaciones;
    }

    public function calcularPercepciones(): float
    {
        return (float) (
            $this->nomina_fiscal +
            $this->diferencia_efectivo +
            $this->horas_extras +
            $this->bonos_otros +
            $this->monto_aguinaldo +
            $this->monto_vacaciones +
            $this->monto_prima_vacacional
        );
    }

    public function calcularDeducciones(): float
    {
        return (float) (
            $this->descuento_adelanto +
            $this->descuento_infonavit +
            $this->otros_descuentos
        );
    }

    public function calcularNeto(): float
    {
        return max(0, $this->calcularPercepciones() - $this->calcularDeducciones());
    }

    public function store(): NominaRecibo
    {
        $validated = $this->validate();
        $empleado = NominaEmpleado::with('area', 'puesto')->findOrFail($this->id_empleado);

        return DB::transaction(function () use ($validated, $empleado) {
            $folio = 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $totalPercepciones = $this->calcularPercepciones();
            $totalDeducciones = $this->calcularDeducciones();
            $neto = $this->calcularNeto();

            $recibo = NominaRecibo::create([
                'folio' => $folio,
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
                'nomina_fiscal' => $this->nomina_fiscal,
                'diferencia_efectivo' => $this->diferencia_efectivo,
                'horas_extras' => $this->horas_extras,
                'bonos_otros' => $this->bonos_otros,
                'monto_aguinaldo' => $this->monto_aguinaldo,
                'monto_vacaciones' => $this->monto_vacaciones,
                'monto_prima_vacacional' => $this->monto_prima_vacacional,
                'total_percepciones' => $totalPercepciones,
                'descuento_adelanto' => $this->descuento_adelanto,
                'descuento_infonavit' => $this->descuento_infonavit,
                'otros_descuentos' => $this->otros_descuentos,
                'total_deducciones' => $totalDeducciones,
                'neto_pagar' => $neto,
                'metodo_pago' => $this->metodo_pago,
                'estado_pago' => $this->estado_pago,
                'fecha_pago_real' => $this->estado_pago === 'PAGADO' ? ($this->fecha_pago_real ?: now()->toDateString()) : null,
                'observaciones' => $this->observaciones,
                'id_usuario' => auth()->id(),
            ]);

            // Si hay descuento de adelanto, registrar abono a la cuenta contable del empleado
            if ($this->descuento_adelanto > 0) {
                $saldoAnterior = (float) $empleado->saldo_adelantos;
                $saldoNuevo = max(0, $saldoAnterior - $this->descuento_adelanto);

                NominaMovimientoCuenta::create([
                    'id_empleado' => $empleado->id,
                    'id_recibo' => $recibo->id,
                    'id_periodo' => $this->id_periodo,
                    'tipo_movimiento' => 'DESCUENTO_NOMINA',
                    'naturaleza' => 'ABONO',
                    'monto' => $this->descuento_adelanto,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo' => $saldoNuevo,
                    'fecha' => now()->toDateString(),
                    'referencia' => "Descuento en recibo {$recibo->folio}",
                    'id_usuario' => auth()->id(),
                ]);

                $empleado->update(['saldo_adelantos' => $saldoNuevo]);
            }

            if ($this->id_periodo) {
                NominaPeriodo::find($this->id_periodo)?->recalcularTotales();
            }

            $this->reset();
            return $recibo;
        });
    }

    public function update(): void
    {
        if (!$this->recibo) {
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $totalPercepciones = $this->calcularPercepciones();
            $totalDeducciones = $this->calcularDeducciones();
            $neto = $this->calcularNeto();

            $this->recibo->update([
                'tipo_recibo' => $this->tipo_recibo,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'fecha_pago' => $this->fecha_pago ?: null,
                'nomina_fiscal' => $this->nomina_fiscal,
                'diferencia_efectivo' => $this->diferencia_efectivo,
                'horas_extras' => $this->horas_extras,
                'bonos_otros' => $this->bonos_otros,
                'monto_aguinaldo' => $this->monto_aguinaldo,
                'monto_vacaciones' => $this->monto_vacaciones,
                'monto_prima_vacacional' => $this->monto_prima_vacacional,
                'total_percepciones' => $totalPercepciones,
                'descuento_adelanto' => $this->descuento_adelanto,
                'descuento_infonavit' => $this->descuento_infonavit,
                'otros_descuentos' => $this->otros_descuentos,
                'total_deducciones' => $totalDeducciones,
                'neto_pagar' => $neto,
                'metodo_pago' => $this->metodo_pago,
                'estado_pago' => $this->estado_pago,
                'fecha_pago_real' => $this->estado_pago === 'PAGADO' ? ($this->fecha_pago_real ?: now()->toDateString()) : null,
                'observaciones' => $this->observaciones,
            ]);

            if ($this->recibo->id_periodo) {
                NominaPeriodo::find($this->recibo->id_periodo)?->recalcularTotales();
            }
        });
    }
}


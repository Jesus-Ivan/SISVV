<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominaRecibo extends Model
{
    use HasFactory;

    protected $table = 'nomina_recibos';

    protected $fillable = [
        'folio',
        'id_periodo',
        'id_empleado',
        'tipo_recibo',
        'id_empleado_snapshot',
        'nombre_empleado_snapshot',
        'area_snapshot',
        'puesto_snapshot',
        'tipo_empleado_snapshot',
        'fecha_inicio',
        'fecha_fin',
        'fecha_pago',
        'nomina_fiscal',
        'diferencia_efectivo',
        'horas_extras',
        'bonos_otros',
        'monto_aguinaldo',
        'monto_vacaciones',
        'monto_prima_vacacional',
        'total_percepciones',
        'descuento_adelanto',
        'descuento_infonavit',
        'otros_descuentos',
        'total_deducciones',
        'neto_pagar',
        'metodo_pago',
        'estado_pago',
        'fecha_pago_real',
        'observaciones',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_pago' => 'date',
        'fecha_pago_real' => 'date',
        'nomina_fiscal' => 'decimal:2',
        'diferencia_efectivo' => 'decimal:2',
        'horas_extras' => 'decimal:2',
        'bonos_otros' => 'decimal:2',
        'monto_aguinaldo' => 'decimal:2',
        'monto_vacaciones' => 'decimal:2',
        'monto_prima_vacacional' => 'decimal:2',
        'total_percepciones' => 'decimal:2',
        'descuento_adelanto' => 'decimal:2',
        'descuento_infonavit' => 'decimal:2',
        'otros_descuentos' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'neto_pagar' => 'decimal:2',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(NominaPeriodo::class, 'id_periodo');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(NominaEmpleado::class, 'id_empleado');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function movimientosCuenta(): HasMany
    {
        return $this->hasMany(NominaMovimientoCuenta::class, 'id_recibo');
    }

    public function calcularTotales(): void
    {
        $this->total_percepciones = (float) (
            $this->nomina_fiscal +
            $this->diferencia_efectivo +
            $this->horas_extras +
            $this->bonos_otros +
            $this->monto_aguinaldo +
            $this->monto_vacaciones +
            $this->monto_prima_vacacional
        );

        $this->total_deducciones = (float) (
            $this->descuento_adelanto +
            $this->descuento_infonavit +
            $this->otros_descuentos
        );

        $this->neto_pagar = max(0, (float) ($this->total_percepciones - $this->total_deducciones));
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominaMovimientoCuenta extends Model
{
    use HasFactory;

    protected $table = 'nomina_movimientos_cuenta';

    protected $fillable = [
        'id_empleado',
        'id_recibo',
        'id_periodo',
        'folio',
        'consecutivo_empleado',
        'tipo_movimiento',
        'naturaleza',
        'monto',
        'saldo_anterior',
        'saldo_nuevo',
        'fecha',
        'referencia',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_nuevo' => 'decimal:2',
        'consecutivo_empleado' => 'integer',
    ];

    /**
     * Obtiene el siguiente número consecutivo disponible para el empleado,
     * reutilizando huecos numéricos si existen.
     */
    public static function obtenerSiguienteConsecutivo(int $idEmpleado): int
    {
        $existentes = static::where('id_empleado', $idEmpleado)
            ->whereNotNull('consecutivo_empleado')
            ->orderBy('consecutivo_empleado', 'asc')
            ->pluck('consecutivo_empleado')
            ->toArray();

        if (empty($existentes)) {
            return 1;
        }

        $esperado = 1;
        foreach ($existentes as $num) {
            if ($num > $esperado) {
                return $esperado;
            }
            $esperado = $num + 1;
        }

        return $esperado;
    }

    /**
     * Genera el siguiente folio con formato PRE-EEEE-CCCC
     * ej: VAL-0015-0001, ABO-0015-0001, DSC-0015-0001, AJU-0015-0001
     */
    public static function generarSiguienteFolio(int $idEmpleado, string $tipoMovimiento = 'ADELANTO_EFECTIVO'): array
    {
        $consecutivo = static::obtenerSiguienteConsecutivo($idEmpleado);
        $prefix = match ($tipoMovimiento) {
            'ADELANTO_EFECTIVO' => 'VAL',
            'ABONO_DIRECTO' => 'ABO',
            'DESCUENTO_NOMINA' => 'DSC',
            'AJUSTE' => 'AJU',
            default => 'MOV',
        };
        $folio = sprintf('%s-%04d-%04d', $prefix, $idEmpleado, $consecutivo);

        return [
            'consecutivo' => $consecutivo,
            'folio' => $folio,
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(NominaEmpleado::class, 'id_empleado');
    }

    public function recibo(): BelongsTo
    {
        return $this->belongsTo(NominaRecibo::class, 'id_recibo');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(NominaPeriodo::class, 'id_periodo');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}


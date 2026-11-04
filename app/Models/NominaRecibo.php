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
        'consecutivo_empleado',
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
        'consecutivo_empleado' => 'integer',
        'nomina_fiscal' => 'decimal:2',
        'diferencia_efectivo' => 'decimal:2',
        'horas_extras' => 'decimal:2',
        'bonos_otros' => 'decimal:2',
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
            $this->bonos_otros
        );

        $this->total_deducciones = (float) (
            $this->descuento_adelanto +
            $this->descuento_infonavit +
            $this->otros_descuentos
        );

        $this->neto_pagar = max(0, (float) ($this->total_percepciones - $this->total_deducciones));
    }

    /**
     * Obtiene el siguiente número consecutivo disponible para un empleado,
     * reutilizando huecos dejados por recibos eliminados o tomando el siguiente al máximo.
     *
     * @param int $idEmpleado
     * @param array $excluir Lista opcional de consecutivos reservados en memoria (ej. importación en lote)
     * @return int
     */
    public static function obtenerSiguienteConsecutivo(int $idEmpleado, array $excluir = []): int
    {
        $existentes = self::where('id_empleado', $idEmpleado)
            ->pluck('consecutivo_empleado')
            ->map(fn($v) => (int) $v)
            ->toArray();

        $ocupados = array_flip(array_merge($existentes, $excluir));

        $consecutivo = 1;
        while (isset($ocupados[$consecutivo])) {
            $consecutivo++;
        }

        return $consecutivo;
    }

    /**
     * Genera el siguiente folio formal y consecutivo para un empleado.
     * Formato: REC-{ID_EMPLEADO 4 dígitos}-{CONSECUTIVO 4 dígitos}
     * Ejemplo: REC-0015-0001
     *
     * @param int $idEmpleado
     * @param array $excluir
     * @return array{consecutivo: int, folio: string}
     */
    public static function generarSiguienteFolio(int $idEmpleado, array $excluir = []): array
    {
        $consecutivo = self::obtenerSiguienteConsecutivo($idEmpleado, $excluir);
        $folio = sprintf('REC-%04d-%04d', $idEmpleado, $consecutivo);

        return [
            'consecutivo' => $consecutivo,
            'folio' => $folio,
        ];
    }
}


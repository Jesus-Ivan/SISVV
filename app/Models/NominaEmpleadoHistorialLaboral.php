<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominaEmpleadoHistorialLaboral extends Model
{
    use HasFactory;

    protected $table = 'nomina_empleados_historial_laboral';

    protected $fillable = [
        'id_empleado',
        'fecha_ingreso',
        'fecha_baja',
        'salario_base',
        'id_puesto',
        'id_area',
        'tipo_empleado',
        'motivo_baja',
        'motivo_reingreso',
        'observaciones',
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_baja' => 'date',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(NominaEmpleado::class, 'id_empleado');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(NominaPuesto::class, 'id_puesto');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(NominaArea::class, 'id_area');
    }

    /**
     * Scope para ciclos que se traslaparon con un rango de fechas.
     * Condición: fecha_ingreso <= fin AND (fecha_baja IS NULL OR fecha_baja >= inicio)
     */
    public function scopeLaborandoEnRango($query, $fechaInicio, $fechaFin)
    {
        return $query->where('fecha_ingreso', '<=', $fechaFin)
            ->where(function ($q) use ($fechaInicio) {
                $q->whereNull('fecha_baja')
                  ->orWhere('fecha_baja', '>=', $fechaInicio);
            });
    }
}


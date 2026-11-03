<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominaPeriodoEmpleado extends Model
{
    use HasFactory;

    protected $table = 'nomina_periodo_empleados';

    protected $fillable = [
        'id_periodo',
        'id_empleado',
        'monto_acordado',
        'notas',
    ];

    protected $casts = [
        'monto_acordado' => 'decimal:2',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(NominaPeriodo::class, 'id_periodo');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(NominaEmpleado::class, 'id_empleado');
    }
}


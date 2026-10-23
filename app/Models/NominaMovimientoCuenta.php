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
    ];

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


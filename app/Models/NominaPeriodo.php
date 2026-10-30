<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominaPeriodo extends Model
{
    use HasFactory;

    protected $table = 'nomina_periodos';

    protected $fillable = [
        'folio',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_pago',
        'tipo_periodo',
        'estado',
        'total_fiscal',
        'total_efectivo',
        'total_percepciones',
        'total_deducciones',
        'total_neto',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_pago' => 'date',
        'total_fiscal' => 'decimal:2',
        'total_efectivo' => 'decimal:2',
        'total_percepciones' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'total_neto' => 'decimal:2',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function recibos(): HasMany
    {
        return $this->hasMany(NominaRecibo::class, 'id_periodo');
    }

    public function getNombrePeriodoAttribute(): string
    {
        return $this->nombre ?? '';
    }

    public function recalcularTotales(): void
    {
        $this->total_fiscal = (float) $this->recibos()->sum('nomina_fiscal');
        $this->total_efectivo = (float) $this->recibos()->sum('diferencia_efectivo');
        $this->total_percepciones = (float) $this->recibos()->sum('total_percepciones');
        $this->total_deducciones = (float) $this->recibos()->sum('total_deducciones');
        $this->total_neto = (float) $this->recibos()->sum('neto_pagar');
        $this->save();
    }
}


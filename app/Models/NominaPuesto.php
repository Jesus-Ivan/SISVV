<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominaPuesto extends Model
{
    use HasFactory;

    protected $table = 'nomina_puestos';

    protected $fillable = [
        'id_area',
        'nombre',
        'salario_sugerido',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'salario_sugerido' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(NominaArea::class, 'id_area');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(NominaEmpleado::class, 'id_puesto');
    }
}


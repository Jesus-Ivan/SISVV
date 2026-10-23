<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class NominaEmpleado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nomina_empleados';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'tipo_empleado',
        'id_area',
        'id_puesto',
        'salario_base',
        'tipo_pago_predeterminado',
        'fecha_ingreso',
        'fecha_baja',
        'activo',
        'foto_path',
        'telefono',
        'rfc',
        'curp',
        'nss',
        'saldo_adelantos',
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'saldo_adelantos' => 'decimal:2',
        'activo' => 'boolean',
        'fecha_ingreso' => 'date',
        'fecha_baja' => 'date',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(NominaArea::class, 'id_area');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(NominaPuesto::class, 'id_puesto');
    }

    public function recibos(): HasMany
    {
        return $this->hasMany(NominaRecibo::class, 'id_empleado');
    }

    public function movimientosCuenta(): HasMany
    {
        return $this->hasMany(NominaMovimientoCuenta::class, 'id_empleado')->orderBy('fecha', 'desc');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_path && Storage::disk('public')->exists($this->foto_path)) {
            return Storage::disk('public')->url($this->foto_path);
        }
        return null;
    }
}


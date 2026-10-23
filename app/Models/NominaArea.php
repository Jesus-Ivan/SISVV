<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominaArea extends Model
{
    use HasFactory;

    protected $table = 'nomina_areas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function puestos(): HasMany
    {
        return $this->hasMany(NominaPuesto::class, 'id_area');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(NominaEmpleado::class, 'id_area');
    }
}

